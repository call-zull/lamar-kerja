<?php
include '../includes/db.php';

if (isset($_GET['mahasiswa_id'])) {
    $mahasiswa_id = $_GET['mahasiswa_id'];

    try {
        $result = [
            'sertifikat' => [],
            'lomba' => [],
            'pelatihan' => [],
            'proyek' => []
        ];

        // Ambil sertifikat
        $sql = "SELECT * FROM sertifikat WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $result['sertifikat'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil lomba
        $sql = "SELECT * FROM lomba WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $result['lomba'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil pelatihan
        $sql = "SELECT * FROM pelatihan WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $result['pelatihan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil proyek
        $sql = "SELECT * FROM proyek WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $result['proyek'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
