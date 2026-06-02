<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$pencukur_id = isset($_POST['pencukur_id']) ? trim($_POST['pencukur_id']) : '';

if ($pencukur_id === '') {
    echo json_encode(["success" => false, "message" => "pencukur_id tidak ditemukan"]);
    exit;
}

$pencukur_id = mysqli_real_escape_string($conn, $pencukur_id);

$query = "
SELECT
    tb_booking.id,
    tb_booking.id AS booking_id,
    tb_booking.user_id,
    tb_booking.pencukur_id,
    tb_booking.booking_date,
    tb_booking.booking_time,
    tb_booking.queue_number,
    tb_booking.status,
    tb_booking.jumlah_orang,
    COALESCE(tb_user.nama, '') AS nama_pelanggan,
    COALESCE(tb_pencukur.nama_pencukur, '') AS nama_pencukur
FROM tb_booking
LEFT JOIN tb_user ON tb_booking.user_id = tb_user.id
LEFT JOIN tb_pencukur ON tb_booking.pencukur_id = tb_pencukur.id
WHERE tb_booking.pencukur_id = '$pencukur_id'
    AND LOWER(TRIM(COALESCE(tb_booking.status, ''))) NOT IN ('selesai', 'done', 'finished')
ORDER BY tb_booking.queue_number ASC, tb_booking.id ASC
";

$result = mysqli_query($conn, $query);
$bookings = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "data" => $bookings
]);

?>