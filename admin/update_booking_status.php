<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

<<<<<<< HEAD
include_once '../config/database.php';

=======
// Menggunakan format PDO sesuai standar file database Anda sebelumnya
// include_once '../config/database.php';  //pemanggilan jika error akan terus dilanjutkan

require '../config/database.php';

$booking_id = $_POST['id'];
$status     = $_POST['status'];

$database = new Database();
$db = $database->getConnection();


// Validasi status
if($status != 'bayar' && $status != 'cancel'){
    echo json_encode([
        "success" => false,
        "message" => "Missing parameter"
    ]);
    exit;
}

>>>>>>> 37b04af5709fb01e090b21b46cf582508cf0caac
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

<<<<<<< HEAD
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id === '' || $status === '') {
=======
if (!isset($_POST['id']) || !isset($_POST['status'])) {
>>>>>>> 37b04af5709fb01e090b21b46cf582508cf0caac
    echo json_encode([
        "success" => false,
        "message" => "Missing parameter"
    ]);
    exit;
}

<<<<<<< HEAD
$allowed_status = [
    'menunggu',
    'selesai',
    'batal',
    'cancelled',
    'belum_bayar',
    'belum bayar',
    'bayar'
];
=======


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
        "message" => "Missing parameter"
    ]);
    exit;
}

$id = $_POST['id'];
$status = trim($_POST['status']);
>>>>>>> 37b04af5709fb01e090b21b46cf582508cf0caac

if (!in_array($status, $allowed_status, true)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid status value"
    ]);
    exit;
}

$id = mysqli_real_escape_string($conn, $id);
$status = mysqli_real_escape_string($conn, $status);

// Sesuaikan status dari UI ke nilai enum database.
if ($status === 'selesai') {
    $status = 'done';
}

$query = mysqli_query($conn, "UPDATE tb_booking SET status = '$status' WHERE id = '$id'");

if ($query) {
    echo json_encode([
        "success" => true,
        "message" => "Status updated successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Update failed"
    ]);
}

?>