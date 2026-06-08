<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak valid']);
    exit;
}

$booking_id   = isset($_POST['booking_id'])   ? trim($_POST['booking_id'])   : '';
$user_id      = isset($_POST['user_id'])      ? trim($_POST['user_id'])      : '';
$new_date     = isset($_POST['booking_date']) ? trim($_POST['booking_date']) : '';
$new_time     = isset($_POST['booking_time']) ? trim($_POST['booking_time']) : '';

if (empty($booking_id) || empty($user_id) || empty($new_date) || empty($new_time)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Escape
$booking_id = mysqli_real_escape_string($conn, $booking_id);
$user_id    = mysqli_real_escape_string($conn, $user_id);
$new_date   = mysqli_real_escape_string($conn, $new_date);
$new_time   = mysqli_real_escape_string($conn, $new_time);

// Validasi: booking harus milik user ini dan status belum bayar
$check = mysqli_query($conn, "
    SELECT id, pencukur_id, booking_date, booking_time, queue_number 
    FROM tb_booking 
    WHERE id = '$booking_id' 
    AND user_id = '$user_id' 
    AND status = 'belum bayar'
");

if (mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking tidak ditemukan atau tidak bisa diubah']);
    exit;
}

$old = mysqli_fetch_assoc($check);
$pencukur_id    = $old['pencukur_id'];
$old_date       = $old['booking_date'];
$old_time       = $old['booking_time'];
$old_queue      = (int) $old['queue_number'];

// Cek bentrok di slot baru
$bentrok = mysqli_query($conn, "
    SELECT id FROM tb_booking
    WHERE pencukur_id = '$pencukur_id'
    AND booking_date = '$new_date'
    AND booking_time = '$new_time'
    AND status = 'belum bayar'
    AND id != '$booking_id'
");

if (mysqli_num_rows($bentrok) > 0) {
    echo json_encode(['success' => false, 'message' => 'Slot waktu sudah terisi, pilih jam lain']);
    exit;
}

// Langkah 1: Geser queue lama (yang di belakang booking ini naik)
mysqli_query($conn, "
    UPDATE tb_booking
    SET queue_number = queue_number - 1
    WHERE pencukur_id = '$pencukur_id'
    AND booking_date = '$old_date'
    AND booking_time > '$old_time'
    AND queue_number IS NOT NULL
    AND status = 'belum bayar'
    AND id != '$booking_id'
");

// Langkah 2: Hitung posisi baru berdasarkan booking_time
$count = mysqli_query($conn, "
    SELECT COUNT(*) as total FROM tb_booking
    WHERE pencukur_id = '$pencukur_id'
    AND booking_date = '$new_date'
    AND booking_time < '$new_time'
    AND queue_number IS NOT NULL
    AND status = 'belum bayar'
    AND id != '$booking_id'
");
$row          = mysqli_fetch_assoc($count);
$new_queue    = (int)$row['total'] + 1;

// Langkah 3: Geser queue baru (yang di belakang posisi baru mundur)
mysqli_query($conn, "
    UPDATE tb_booking
    SET queue_number = queue_number + 1
    WHERE pencukur_id = '$pencukur_id'
    AND booking_date = '$new_date'
    AND booking_time >= '$new_time'
    AND queue_number IS NOT NULL
    AND status = 'belum bayar'
    AND id != '$booking_id'
");

// Langkah 4: Update booking
mysqli_query($conn, "
    UPDATE tb_booking
    SET booking_date = '$new_date',
        booking_time = '$new_time',
        queue_number = '$new_queue'
    WHERE id = '$booking_id'
");

echo json_encode([
    'success'      => true,
    'queue_number' => $new_queue,
    'message'      => 'Jadwal berhasil diubah! Nomor antrian baru: ' . $new_queue
]);
?>