<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_barbershop";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal");
}

?>