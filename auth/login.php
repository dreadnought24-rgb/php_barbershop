<?php

header("Content-Type: application/json");

require '../config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM tb_user WHERE username='$username'");

if(mysqli_num_rows($query) > 0){

    $user = mysqli_fetch_assoc($query);

    if(password_verify($password, $user['password'])){

        echo json_encode([
            "success" => true,
            "message" => "Login berhasil",
            "data" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "nama" => $user['nama'],
                "role" => $user['role']
            ]
        ]);

    }else{

        echo json_encode([
            "success" => false,
            "message" => "Password salah"
        ]);

    }

}else{

    echo json_encode([
        "success" => false,
        "message" => "User tidak ditemukan"
    ]);

}

?>