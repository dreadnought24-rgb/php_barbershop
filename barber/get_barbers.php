<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../config/database.php";
// Query mengambil semua barber
$query = mysqli_query(
    $conn,
    "SELECT * FROM tb_pencukur"
);

if (!$query) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data barber dari database",
    ]);
    exit;
}

// Array kosong
$data = [];

// Mengambil data satu per satu
while($row = mysqli_fetch_assoc($query)){

    // Memasukkan ke array
    $data[] = $row;
}

// Mengubah menjadi JSON
echo json_encode($data);