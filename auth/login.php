<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '../config/database.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($username === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Username dan password tidak boleh kosong"]);
    exit;
}

$username = mysqli_real_escape_string($conn, $username);

$query = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username'");

if (mysqli_num_rows($query) > 0) {
    $user = mysqli_fetch_assoc($query);

    if (password_verify($password, $user['password'])) {
        if ($user['role'] != 'admin' && $user['role'] != 'user') {
            echo json_encode(["success" => false, "message" => "Role tidak valid"]);
            exit;
        }
        echo json_encode(["success" => true, "role" => $user['role'], "id" => (int)$user['id'], "message" => "Login berhasil"]);
    } else {
        echo json_encode(["success" => false, "message" => "Password salah"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User tidak ditemukan"]);
}
?>