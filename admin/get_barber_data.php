<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';

if ($user_id === '') {
    echo json_encode(["success" => false, "message" => "user_id tidak ditemukan"]);
    exit;
}

<<<<<<< HEAD

$user_id = mysqli_real_escape_string($conn, $user_id);

$query = "SELECT id_pencukur, nama_pencukur FROM tb_pencukur WHERE id_pencukur = :user_id LIMIT 1";


// $query = mysqli_query(
//     $conn,
//     "SELECT pencukur_id, nama_pencukur, status FROM tb_pencukur WHERE user_id = '$user_id' LIMIT 1"
// );
=======
$user_id = mysqli_real_escape_string($conn, $user_id);

// Kolom id_pencukur di tb_pencukur berisi user_id dari tb_user
$query = mysqli_query(
    $conn,
    "SELECT id, id_pencukur, nama_pencukur, status FROM tb_pencukur WHERE id_pencukur = '$user_id' LIMIT 1"
);
>>>>>>> c632ad967205cca01e5a44c1a7492bc22470ae39

if ($query && mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);

    echo json_encode([
        "success" => true,
        "id" => (int) $row['id'],
        "pencukur_id" => (int) $row['id'],
        "nama_pencukur" => $row['nama_pencukur'],
        "status" => $row['status'] ?? ''
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Data pencukur tidak ditemukan untuk user ini."
    ]);
}

?>