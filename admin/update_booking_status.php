<?php
header("Content-Type: application/json");
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing parameter"
    ]);
    exit;
}

$id = (int) $_POST['id'];
$status = trim($_POST['status']);

$allowed_status = ['belum bayar', 'bayar', 'cancelled'];

if (!in_array($status, $allowed_status)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid status value"
    ]);
    exit;
}

$query = "UPDATE tb_booking SET status = ? WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
    exit;
}

$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Status updated successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No data updated"
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Update failed"
    ]);
}

$stmt->close();
$conn->close();
?>