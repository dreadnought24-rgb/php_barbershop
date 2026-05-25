<?php

header("Content-Type: application/json");

require '../config/database.php';

$booking_id = $_POST['booking_id'];
$status     = $_POST['status'];

// Validasi status
if($status != 'bayar' && $status != 'cancel'){

    echo json_encode([
        "success" => false,
        "message" => "Status tidak valid"
    ]);

    exit;
}

$query = mysqli_query($conn,
"UPDATE tb_booking 
SET status='$status'
WHERE booking_id='$booking_id'");

if($query){

    echo json_encode([
        "success" => true,
        "message" => "Status updated"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Gagal update status"
    ]);

}

?>