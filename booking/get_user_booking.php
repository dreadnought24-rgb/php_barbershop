<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include_once '../config/database.php';

$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';

if ($user_id === '') {
    echo json_encode(["success" => false, "message" => "user_id belum dikirim"]);
    exit;
}

$user_id = mysqli_real_escape_string($conn, $user_id);

$query = mysqli_query($conn, "
    SELECT
        tb_booking.id AS booking_id,
        tb_booking.booking_date,
        tb_booking.booking_time,
        tb_booking.queue_number,
        tb_booking.status,
        tb_pencukur.nama_pencukur
    FROM tb_booking
    JOIN tb_pencukur ON tb_booking.pencukur_id = tb_pencukur.id
    WHERE tb_booking.user_id = '$user_id'
    ORDER BY tb_booking.id DESC
    LIMIT 1
");

if ($query && mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Booking tidak ditemukan"]);
}
?>