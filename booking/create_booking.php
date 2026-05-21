<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 1. Hubungkan ke file konfigurasi database kamu
include_once '../config/database.php';

// Pastikan request method-nya adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. Tangkap parameter POST dari Flutter
    $user_id       = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $pencukur_id   = isset($_POST['pencukur_id']) ? $_POST['pencukur_id'] : '';
    $booking_date  = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
    $booking_time  = isset($_POST['booking_time']) ? $_POST['booking_time'] : '';
    $jumlah_orang  = isset($_POST['jumlah_orang']) ? $_POST['jumlah_orang'] : '';

    // Validasi input data (tidak boleh kosong)
    if (empty($user_id) || empty($pencukur_id) || empty($booking_date) || empty($booking_time) || empty($jumlah_orang)) {
        echo json_encode([
            "success" => false,
            "message" => "Gagal: Data yang dikirim tidak lengkap."
        ]);
        exit;
    }

    // Amankan input dari SQL Injection (Gaya MySQLi)
    $user_id       = mysqli_real_escape_string($conn, $user_id);
    $pencukur_id   = mysqli_real_escape_string($conn, $pencukur_id);
    $booking_date  = mysqli_real_escape_string($conn, $booking_date);
    $booking_time  = mysqli_real_escape_string($conn, $booking_time);
    $jumlah_orang  = mysqli_real_escape_string($conn, $jumlah_orang);

    // === SKENARIO PENGUJIAN 3: VALIDASI SLOT BENTROK ===
    // Memeriksa apakah pencukur tersebut sudah memiliki booking aktif di tanggal & jam yang sama
    $query_cek = "SELECT id FROM tb_booking
    WHERE pencukur_id = '$pencukur_id'
    AND booking_date = '$booking_date'
    AND booking_time = '$booking_time'
    AND status != 'batal'";
    
    $result_cek = mysqli_query($conn, $query_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        // Jika slot waktu sudah terisi, kirim respon gagal (Slot Bentrok Ditolak)
        echo json_encode([
            "success" => false,
            "message" => "Slot waktu bentrok! Silakan pilih jam atau pencukur lain."
        ]);
        exit;
    }

    // === SKENARIO PENGUJIAN 2: GENERATE NO QUEUE (ENQUEUE) ===
    // Mengambil nomor antrean tertinggi pada tanggal tersebut untuk pencukur terkait
    $query_queue = "SELECT MAX(queue_number) as max_queue FROM tb_booking
                    WHERE booking_date = '$booking_date'
                    AND pencukur_id = '$pencukur_id'";
                    
    $result_queue = mysqli_query($conn, $query_queue);
    $row_queue = mysqli_fetch_assoc($result_queue);

    // Aturan Queue: Jika belum ada antrean hari itu, mulai dari 1. Jika sudah ada, inkremen +1
    $queue_number = ($row_queue['max_queue'] !== null) ? $row_queue['max_queue'] + 1 : 1;

    // === SKENARIO PENGUJIAN 1: INSERT DATA KE tb_booking ===
    $query_insert = "INSERT INTO tb_booking (user_id, pencukur_id, booking_date, booking_time, queue_number, jumlah_orang, status, created_at) 
    VALUES ('$user_id', '$pencukur_id', '$booking_date', '$booking_time', '$queue_number', '$jumlah_orang', 'menunggu', NOW())";

    if (mysqli_query($conn, $query_insert)) {
        // Output JSON sukses sesuai target format tugas akhirmu
        echo json_encode([
            "success" => true,
            "queue_number" => (int)$queue_number,
            "message" => "Booking berhasil"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Gagal menyimpan data ke database: " . mysqli_error($conn)
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Metode request tidak valid!"
    ]);
}
?>