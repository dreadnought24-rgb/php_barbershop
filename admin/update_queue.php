<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config/database.php';

$booking_id = $_POST['booking_id'] ?? '';
$pencukur_id = $_POST['pencukur_id'] ?? '';

if (empty($booking_id) || empty($pencukur_id)) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
    exit;
}

// Langkah 1: Ambil queue_numbermilik booking yang selesai
$stmt = $conn->prepare("SELECT queue_number FROM tb_booking WHERE id = ?");$stmt->bind_param("s", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Booking tidak ditemukan']);
    exit;
}

$doneQueueNum = (int) $row['queue_number'];

if ($doneQueueNum <= 0) {
    echo json_encode(['success' => false, 'message' => 'Queue sudah null atau tidak valid']);
    exit;
}

// Langkah 2: Set queue_numberbooking yang selesai menjadi NULL
$stmt = $conn->prepare("UPDATE tb_booking SET queue_number = NULL WHERE id = ?");$stmt->bind_param("s", $booking_id);
$stmt->execute();
$stmt->close();

// Langkah 3: Geser semua antrian di belakangnya (turunkan 1)
$stmt = $conn->prepare("
    UPDATE tb_booking 
    SET queue_number = queue_number - 1
    WHERE pencukur_id = ?
    AND queue_number > ?
    AND queue_number IS NOT NULL
    AND DATE(booking_date) = CURDATE()
");

$stmt->bind_param("si", $pencukur_id, $doneQueueNum);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'message' => 'Queue berhasil diperbarui']);
?>