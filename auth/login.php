<?php

header("Content-Type: application/json");

require '../config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM tb_user WHERE username='$username'"
);

if(mysqli_num_rows($query) > 0){

    $user = mysqli_fetch_assoc($query);

    if(password_verify($password, $user['password'])){

        if($user['role'] != 'admin' && $user['role'] != 'user'){

            echo json_encode([
                "success" => false,
                "message" => "Role tidak valid"
            ]);

            exit;
        }

        echo json_encode([
            "success" => true,
            "role" => $user['role'],
            "id" => (int)$user['id'],
            "message" => "Login berhasil"
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