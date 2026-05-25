<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "user_id tidak ditemukan"]);
    exit();
}

$query = "SELECT pencukur_id, nama_pencukur FROM tb_pencukur WHERE user_id = :user_id LIMIT 1";

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            "success" => true,
            "pencukur_id" => (int)$row['pencukur_id'],
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
}
?>