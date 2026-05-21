<?php

header("Content-Type: application/json");

include "../config/database.php";


if(isset($_GET['user_id'])){

    $user_id = $_GET['user_id'];

}else{

    echo json_encode([
        "success" => false,
        "message" => "user_id belum dikirim"
    ]);

    exit();

}

$query = "
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
";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){

    $data = mysqli_fetch_assoc($result);

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

?>


<!-- http://localhost/barbershop_api/booking/get_user_booking.php?user_id=1 -->