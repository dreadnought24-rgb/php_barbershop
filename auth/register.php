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
$nama     = isset($_POST['nama'])     ? trim($_POST['nama'])     : '';
$no_hp    = isset($_POST['no_hp'])    ? trim($_POST['no_hp'])    : '';

if ($username === '' || $password === '' || $nama === '' || $no_hp === '') {
    echo json_encode(["success" => false, "message" => "Semua field harus diisi"]);
    exit;
}

$u = mysqli_real_escape_string($conn, $username);
$check = mysqli_query($conn, "SELECT id FROM tb_user WHERE username='$u'");

if (mysqli_num_rows($check) > 0) {
    echo json_encode(["success" => false, "message" => "Username sudah digunakan"]);
    exit;
}

$hash  = password_hash($password, PASSWORD_DEFAULT);
$n     = mysqli_real_escape_string($conn, $nama);
$hp    = mysqli_real_escape_string($conn, $no_hp);

$query = mysqli_query($conn, "INSERT INTO tb_user(username,password,nama,no_hp) VALUES('$u','$hash','$n','$hp')");

if ($query) {
    echo json_encode(["success" => true, "message" => "Register berhasil"]);
} else {
    echo json_encode(["success" => false, "message" => "Register gagal: " . mysqli_error($conn)]);
}
?>