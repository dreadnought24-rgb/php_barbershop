<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id === '' || $status === '') {
    echo json_encode([
        "success" => false,
        "message" => "Missing parameter"
    ]);
    exit;
}

$allowed_status = [
    'menunggu',
    'selesai',
    'batal',
    'cancelled',
    'belum_bayar',
    'belum bayar',
    'bayar'
];

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