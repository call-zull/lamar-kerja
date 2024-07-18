<?php
include '../includes/db.php';

$jurusanId = isset($_GET['jurusan_id']) ? (int)$_GET['jurusan_id'] : 0;

// Initialize data arrays
$data = [
    'lomba' => [],
    'pelatihan' => [],
    'sertifikasi' => [],
    'proyek' => [],
    'categories' => []
];

if ($jurusanId > 0) {
    // Fetch data counts grouped by date for the selected jurusan
    $tables = ['lomba', 'pelatihan', 'sertifikasi', 'proyek'];
    foreach ($tables as $table) {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count FROM $table WHERE jurusan_id = :jurusan_id GROUP BY DATE(created_at)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['jurusan_id' => $jurusanId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $date = $result['date'];
            $count = $result['count'];
            $data[$table][] = $count;
            if (!in_array($date, $data['categories'])) {
                $data['categories'][] = $date;
            }
        }
    }
}

echo json_encode($data);
