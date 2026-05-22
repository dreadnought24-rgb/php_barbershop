<?php
$conn = mysqli_connect("localhost", "root", "", "db_barbershop");

// AMBIL DUA PARAMETER: tanggal dan id_pencukur
$tanggal = $_GET['tanggal'];
$id_pencukur = $_GET['id_pencukur'];

$allSlots = ["09.00", "10.00", "11.00", "12.00", "13.00"];

// Query disesuaikan agar hanya mengunci slot untuk pencukur tertentu saja
$query = mysqli_query(
    $conn,
    "SELECT jam FROM tb_booking WHERE tanggal='$tanggal' AND id_pencukur='$id_pencukur'"
);

$bookedSlots = [];
while($row = mysqli_fetch_assoc($query)){
    $bookedSlots[] = $row['jam'];
}

$availableSlots = array_values(array_diff($allSlots, $bookedSlots));

// Sesuai standarisasi Flutter kamu, bungkus dalam format JSON biasa
echo json_encode($availableSlots);
?>