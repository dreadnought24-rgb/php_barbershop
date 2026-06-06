<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Metode request tidak valid"]);
    exit;
}

// ── 1. TAMBAHKAN PENANGKAPAN DATA SERVICE DI SINI ────────────────────────────
$user_id      = isset($_POST['user_id'])      ? trim($_POST['user_id'])      : '';
$pencukur_id  = isset($_POST['pencukur_id'])  ? trim($_POST['pencukur_id'])  : '';
$booking_date = isset($_POST['booking_date']) ? trim($_POST['booking_date']) : '';
$booking_time = isset($_POST['booking_time']) ? trim($_POST['booking_time']) : '';
$service = isset($_POST['layanan']) ? trim($_POST['layanan']) : ''; // ◄ Menangkap 'service' dari Flutter

// Validasi data lengkap termasuk service
if (empty($user_id) || empty($pencukur_id) || empty($booking_date) || empty($booking_time) || empty($service)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap: user_id, pencukur_id, booking_date, booking_time, dan service wajib diisi."]);
    exit;
}

$user_id      = mysqli_real_escape_string($conn, $user_id);
$pencukur_id  = mysqli_real_escape_string($conn, $pencukur_id);
$booking_date = mysqli_real_escape_string($conn, $booking_date);
$booking_time = mysqli_real_escape_string($conn, $booking_time);
$service      = mysqli_real_escape_string($conn, $service); // ◄ Escape string untuk keamanan SQL Injection

// Cek bentrok: hanya booking berstatus 'belum bayar' yang dianggap aktif
$query_cek = "SELECT id FROM tb_booking
    WHERE pencukur_id = '$pencukur_id'
    AND booking_date = '$booking_date'
    AND booking_time = '$booking_time'
    AND status = 'belum bayar'";

$result_cek = mysqli_query($conn, $query_cek);
if (mysqli_num_rows($result_cek) > 0) {
    echo json_encode(["success" => false, "message" => "Slot waktu sudah terisi. Silakan pilih jam atau barber lain."]);
    exit;
}

// Generate queue number
$query_queue  = "SELECT MAX(queue_number) as max_queue FROM tb_booking
    WHERE booking_date = '$booking_date' AND pencukur_id = '$pencukur_id'";
$result_queue = mysqli_query($conn, $query_queue);
$row_queue    = mysqli_fetch_assoc($result_queue);
$queue_number = ($row_queue['max_queue'] !== null) ? (int)$row_queue['max_queue'] + 1 : 1;

// ── 2. PERBARUI QUERY INSERT (Masukkan kolom 'layanan' dan variabel '$service') ──
$query_insert = "INSERT INTO tb_booking (user_id, pencukur_id, booking_date, booking_time, queue_number, status, layanan)
    VALUES ('$user_id', '$pencukur_id', '$booking_date', '$booking_time', '$queue_number', 'belum bayar', '$service')";

if (mysqli_query($conn, $query_insert)) {
    echo json_encode([
        "success"      => true,
        "queue_number" => $queue_number,
        "message"      => "Booking berhasil! Nomor antrian Anda: $queue_number"
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan: " . mysqli_error($conn)]);
}
?>