<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config/database.php';

$today = date('Y-m-d');

// Ambil max_queue semua barber (available + offline)
$query = "
    SELECT 
        tb_pencukur.id AS pencukur_id,
        tb_pencukur.nama_pencukur,
        tb_pencukur.status,
        COALESCE(MAX(tb_booking.queue_number), 0) AS max_queue
    FROM tb_pencukur
    LEFT JOIN tb_booking 
        ON tb_pencukur.id = tb_booking.pencukur_id
        AND DATE(tb_booking.booking_date) = '$today'
        AND tb_booking.queue_number IS NOT NULL
    GROUP BY tb_pencukur.id, tb_pencukur.nama_pencukur, tb_pencukur.status
    ORDER BY max_queue DESC
";

$result  = mysqli_query($conn, $query);
$barbers = [];

while ($row = mysqli_fetch_assoc($result)) {
    $barbers[] = [
        'pencukur_id' => $row['pencukur_id'],
        'nama'        => $row['nama_pencukur'],
        'status'      => $row['status'],
        'max_queue'   => (int) $row['max_queue'],
    ];
}

if (count($barbers) < 2) {
    echo json_encode(['success' => true, 'action' => 'none', 'message' => 'Tidak cukup barber']);
    exit;
}

$actions = [];

// ── BAGIAN 1: Cek barber offline → kembalikan ke available ──────────────────
$offlineBarbers   = array_filter($barbers, fn($b) => $b['status'] === 'offline');
$availableBarbers = array_filter($barbers, fn($b) => $b['status'] === 'available');

foreach ($offlineBarbers as $offline) {
    if (empty($availableBarbers)) break;

    // Cari barber available dengan queue terdekat (selisih terkecil)
    $minSelisih  = PHP_INT_MAX;
    $terdekat    = null;

    foreach ($availableBarbers as $avail) {
        $selisih = abs($offline['max_queue'] - $avail['max_queue']);
        if ($selisih < $minSelisih) {
            $minSelisih = $selisih;
            $terdekat   = $avail;
        }
    }

    // Jika selisih < 4 → kembalikan ke available
    if ($terdekat !== null && $minSelisih < 4) {
        $id     = $offline['pencukur_id'];
        $update = "UPDATE tb_pencukur SET status = 'available' WHERE id = '$id'";
        mysqli_query($conn, $update);
        $actions[] = "{$offline['nama']} kembali available (selisih $minSelisih vs {$terdekat['nama']})";
    }
}

// ── BAGIAN 2: Cek barber available → set offline jika selisih ≥ 4 ──────────
// Re-fetch setelah perubahan di bagian 1
$result2  = mysqli_query($conn, $query);
$barbers2 = [];

while ($row = mysqli_fetch_assoc($result2)) {
    $barbers2[] = [
        'pencukur_id' => $row['pencukur_id'],
        'nama'        => $row['nama_pencukur'],
        'status'      => $row['status'],
        'max_queue'   => (int) $row['max_queue'],
    ];
}

$availableOnly = array_values(array_filter($barbers2, fn($b) => $b['status'] === 'available'));

if (count($availableOnly) >= 2) {
    // Sudah terurut DESC, [0] = terbanyak, [1] = terdekat
    $terbanyak = $availableOnly[0];
    $terdekat  = $availableOnly[1];
    $selisih   = $terbanyak['max_queue'] - $terdekat['max_queue'];

    if ($selisih >= 4) {
        $id     = $terbanyak['pencukur_id'];
        $update = "UPDATE tb_pencukur SET status = 'offline' WHERE id = '$id'";
        mysqli_query($conn, $update);
        $actions[] = "{$terbanyak['nama']} diset offline (selisih $selisih vs {$terdekat['nama']})";
    }
}

echo json_encode([
    'success' => true,
    'action'  => empty($actions) ? 'none' : 'updated',
    'message' => empty($actions) ? 'Tidak ada perubahan status' : implode(', ', $actions),
]);
?>