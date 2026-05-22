<?php

header("Content-Type: application/json");

include "../config/database.php";
// Query mengambil semua barber
$query = mysqli_query(
    $conn,
    "SELECT * FROM tb_pencukur"
);

// Array kosong
$data = [];

// Mengambil data satu per satu
while($row = mysqli_fetch_assoc($query)){

    // Memasukkan ke array
    $data[] = $row;
}

// Mengubah menjadi JSON
echo json_encode($data);