<?php

// Koneksi database
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_barbershop"
);

// Mengambil tanggal dari Flutter
$tanggal = $_GET['tanggal'];

// Semua slot yang tersedia
$allSlots = [

    "09.00",
    "10.00",
    "11.00",
    "12.00",
    "13.00"
];

// Query slot yang sudah dibooking
$query = mysqli_query(

    $conn,

    "SELECT jam
     FROM tb_booking
     WHERE tanggal='$tanggal'"
);

// Array slot penuh
$bookedSlots = [];

// Mengambil data booking
while($row = mysqli_fetch_assoc($query)){

    // Memasukkan jam booking
    $bookedSlots[] = $row['jam'];
}

// Menghapus slot penuh
$availableSlots = array_values(

    array_diff(
        $allSlots,
        $bookedSlots
    )
);

// Mengirim JSON slot kosong
echo json_encode(
    $availableSlots
);