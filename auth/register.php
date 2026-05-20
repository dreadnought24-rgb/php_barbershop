<?php

header("Content-Type: application/json");

require '../config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];
$nama      = $_POST['nama'];
$no_hp     = $_POST['no_hp'];

// cek username sudah ada atau belum
$check = mysqli_query($conn,
    "SELECT * FROM tb_user WHERE username='$username'");

if(mysqli_num_rows($check) > 0){

    echo json_encode([
        "success" => false,
        "message" => "Username sudah digunakan"
    ]);

    exit;
}

// hash password
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

$query = mysqli_query($conn,
"INSERT INTO tb_user(username,password,nama,no_hp)
VALUES('$username','$hashPassword','$nama','$no_hp')");

if($query){

    echo json_encode([
        "success" => true,
        "message" => "Register berhasil"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Register gagal"
    ]);

}

?>