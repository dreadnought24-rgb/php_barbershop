<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';

if (empty($user_id)) {
    echo json_encode(["success" => false, "message" => "User ID tidak valid."]);
    exit;
}

$user_id = mysqli_real_escape_string($conn, $user_id);
$query = mysqli_query($conn, "SELECT username, nama, no_hp FROM tb_user WHERE id = '$user_id'");

if ($row = mysqli_fetch_assoc($query)) {
    echo json_encode([
        "success" => true,
        "data" => [
            "username" => $row['username'],
            "nama" => $row['nama'],
            "no_hp" => $row['no_hp']
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "User tidak ditemukan."]);
}
?>