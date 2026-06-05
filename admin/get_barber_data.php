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


$user_id = mysqli_real_escape_string($conn, $user_id);

$query = "SELECT id_pencukur, nama_pencukur FROM tb_pencukur WHERE id_pencukur = :user_id LIMIT 1";


// $query = mysqli_query(
//     $conn,
//     "SELECT pencukur_id, nama_pencukur, status FROM tb_pencukur WHERE user_id = '$user_id' LIMIT 1"
// );

<<<<<<< HEAD
if ($query && mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);

    echo json_encode([
        "success" => true,
        "id" => (int) $row['pencukur_id'],
        "pencukur_id" => (int) $row['pencukur_id'],
        "nama_pencukur" => $row['nama_pencukur'],
        "status" => $row['status'] ?? ''
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Data pencukur tidak ditemukan untuk user ini."
    ]);
=======
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            "success" => true,
            "pencukur_id" => (int)$row['id_pencukur'],
            "nama_pencukur" => $row['nama_pencukur']
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Data pencukur tidak ditemukan untuk user ini."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error database: " . $e->getMessage()]);
>>>>>>> 37b04af5709fb01e090b21b46cf582508cf0caac
}

?>