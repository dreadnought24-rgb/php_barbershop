<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Menggunakan format PDO sesuai standar file database Anda sebelumnya
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

// Mengecek parameter 'id' yang dikirim dari Flutter
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing parameter"
    ]);
    exit;
}

$id = $_POST['id'];
$status = trim($_POST['status']);

$allowed_status = ['belum bayar', 'bayar', 'cancelled'];

if (!in_array($status, $allowed_status)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid status value"
    ]);
    exit;
}

// Query menggunakan parameter :id sesuai struktur tabel database Anda
$query = "UPDATE tb_booking SET status = :status WHERE id = :id";

try {
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Status updated successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Update failed"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>