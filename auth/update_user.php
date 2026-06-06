<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$column  = isset($_POST['column'])  ? trim($_POST['column'])  : ''; // Kolom database (username/nama/no_hp/password)
$value   = isset($_POST['value'])   ? trim($_POST['value'])   : '';

if (empty($user_id) || empty($column) || empty($value)) {
    echo json_encode(["success" => false, "message" => "Data pembaruan tidak lengkap."]);
    exit;
}

$user_id = mysqli_real_escape_string($conn, $user_id);
$value   = mysqli_real_escape_string($conn, $value);

// Validasi nama kolom untuk keamanan SQL Injection khusus kolom
$allowed_columns = ['username', 'nama', 'no_hp', 'password'];
if (!in_array($column, $allowed_columns)) {
    echo json_encode(["success" => false, "message" => "Kolom tidak valid."]);
    exit;
}

// Jika yang diupdate password, lakukan enkripsi md5 atau password_hash (sesuaikan sistem login Anda)
if ($column === 'password') {
    $value = md5($value); // Ganti password_hash() jika login Anda menggunakan Bcrypt
}

$query_update = "UPDATE tb_user SET $column = '$value' WHERE id = '$user_id'";

if (mysqli_query($conn, $query_update)) {
    echo json_encode(["success" => true, "message" => "Berhasil memperbarui data di database!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui: " . mysqli_error($conn)]);
}
?>