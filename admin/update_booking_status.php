<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$id     = isset($_POST['id'])     ? trim($_POST['id'])     : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id === '' || $status === '') {
    echo json_encode(["success" => false, "message" => "Missing parameter"]);
    exit;
}

// Nilai yang diizinkan sesuai enum tb_booking
$allowed = ['belum bayar', 'bayar', 'cancel'];

if (!in_array($status, $allowed, true)) {
    echo json_encode(["success" => false, "message" => "Status tidak valid: $status"]);
    exit;
}

$id     = mysqli_real_escape_string($conn, $id);
$status = mysqli_real_escape_string($conn, $status);

$query = mysqli_query($conn, "UPDATE tb_booking SET status = '$status' WHERE id = '$id'");

if ($query) {
    echo json_encode(["success" => true, "message" => "Status berhasil diperbarui"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal update: " . mysqli_error($conn)]);
}

?>