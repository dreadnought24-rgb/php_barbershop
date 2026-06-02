<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include_once '../config/database.php';

$query = "
SELECT
    tb_booking.id AS booking_id,
    tb_booking.user_id,
    tb_booking.pencukur_id,
    tb_booking.booking_date,
    tb_booking.booking_time,
    tb_booking.queue_number,
    tb_booking.status,
    tb_booking.jumlah_orang,
    COALESCE(tb_pencukur.nama_pencukur, '') AS nama_pencukur,
    COALESCE(tb_user.nama, '') AS nama_pelanggan
FROM tb_booking
LEFT JOIN tb_pencukur ON tb_booking.pencukur_id = tb_pencukur.id
LEFT JOIN tb_user ON tb_booking.user_id = tb_user.id
ORDER BY tb_booking.id DESC
";

$result = mysqli_query($conn, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "data" => $data
]);

?>