<?php
header("Content-Type: application/json");
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $query = "UPDATE tb_booking SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $booking_id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Status updated"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed update"
        ]);
    }
}
?>