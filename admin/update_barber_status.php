<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode([
		"success" => false,
		"message" => "Invalid request method"
	]);
	exit;
}

$pencukur_id = isset($_POST['pencukur_id']) ? trim($_POST['pencukur_id']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($pencukur_id === '' || $status === '') {
	echo json_encode([
		"success" => false,
		"message" => "Missing parameter"
	]);
	exit;
}

$pencukur_id = mysqli_real_escape_string($conn, $pencukur_id);
$status = mysqli_real_escape_string($conn, $status);

$query = mysqli_query($conn, "UPDATE tb_pencukur SET status = '$status' WHERE id = '$pencukur_id' OR pencukur_id = '$pencukur_id'");

if ($query) {
	echo json_encode([
		"success" => true,
		"message" => "Barber status updated successfully"
	]);
} else {
	echo json_encode([
		"success" => false,
		"message" => "Update failed"
	]);
}

?>
