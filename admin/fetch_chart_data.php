<?php
include '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $jurusan_id = isset($_GET['jurusan_id']) ? $_GET['jurusan_id'] : 0;

    $data = [
        'lomba' => [],
        'pelatihan' => [],
        'sertifikasi' => [],
        'proyek' => []
    ];

    if ($jurusan_id > 0) {
        $tables = ['lomba', 'pelatihan', 'sertifikasi', 'proyek'];
        
        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as count, DATE(created_at) as date FROM $table
                    INNER JOIN mahasiswas ON $table.mahasiswa_id = mahasiswas.id
                    WHERE mahasiswas.jurusan_id = :jurusan_id
                    GROUP BY DATE(created_at)
                    ORDER BY DATE(created_at)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['jurusan_id' => $jurusan_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                $data[$table][] = [
                    'date' => $row['date'],
                    'count' => (int) $row['count']
                ];
            }
        }
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
