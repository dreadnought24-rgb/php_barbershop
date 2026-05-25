<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$pencukur_id = isset($_POST['pencukur_id']) ? $_POST['pencukur_id'] : null;

if (!$pencukur_id) {
    echo json_encode(["success" => false, "message" => "pencukur_id tidak ditemukan"]);
    exit();
}

// Query mengambil antrean yang difilter HANYA untuk pencukur yang bersangkutan
$query = "SELECT * FROM tb_booking WHERE pencukur_id = :pencukur_id ORDER BY tanggal_booking ASC, jam_booking ASC";

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":pencukur_id", $pencukur_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $bookings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = $row;
        }
        echo json_encode([
            "success" => true,
            "bookings" => $bookings
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Tidak ada data antrean untuk pencukur ini."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error database: " . $e->getMessage()]);
}
?>