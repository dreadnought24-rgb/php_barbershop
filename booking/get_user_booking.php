<?php

header("Content-Type: application/json");

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_barbershop"
);

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Koneksi database gagal"
    ]);
    exit;
}

$user_id = $_GET['user_id'];

$query = mysqli_query($conn, "
SELECT
    tb_booking.id,
    tb_booking.booking_date,
    tb_booking.booking_time,
    tb_booking.queue_number,
    tb_booking.status,
    tb_pencukur.nama_pencukur
FROM tb_booking
JOIN tb_pencukur
ON tb_booking.pencukur_id = tb_pencukur.id
WHERE tb_booking.user_id = '$user_id'
ORDER BY tb_booking.id DESC
LIMIT 1
");

if(mysqli_num_rows($query) > 0){

    $data = mysqli_fetch_assoc($query);

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Booking tidak ditemukan"
    ]);
}