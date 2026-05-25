<?php

header("Content-Type: application/json");

require '../config/database.php';

$id     = $_POST['id'];
$status = $_POST['status'];

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
WHERE id='$id'");

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