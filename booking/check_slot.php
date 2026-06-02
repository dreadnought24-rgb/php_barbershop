<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include_once '../config/database.php';

$tanggal = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : '';
$id_pencukur = isset($_GET['id_pencukur']) ? trim($_GET['id_pencukur']) : '';

if ($tanggal === '' || $id_pencukur === '') {
    echo json_encode([]);
    exit;
}

$allSlots = ["09:00", "10:00", "11:00", "12:00", "13:00"];

$tanggal = mysqli_real_escape_string($conn, $tanggal);
$id_pencukur = mysqli_real_escape_string($conn, $id_pencukur);

$query = mysqli_query(
    $conn,
    "SELECT booking_time FROM tb_booking WHERE booking_date = '$tanggal' AND pencukur_id = '$id_pencukur' AND LOWER(TRIM(COALESCE(status, ''))) NOT IN ('done', 'cancelled', 'batal')"
);

$bookedSlots = [];
while ($row = mysqli_fetch_assoc($query)) {
    $bookingTime = trim((string) $row['booking_time']);
    $bookingTime = str_replace('.', ':', $bookingTime);
    $bookedSlots[] = substr($bookingTime, 0, 5);
}

$availableSlots = array_values(array_diff($allSlots, $bookedSlots));

echo json_encode($availableSlots);

?>