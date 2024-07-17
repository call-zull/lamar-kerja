<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $lowongan_id = $_POST['lowongan_id'];
    $pesan = $_POST['pesan'];
    $status = 'Pending';  // or any default value you prefer

    // Retrieve mahasiswa_id from session
    if (isset($_SESSION['mahasiswa_id'])) {
        $mahasiswa_id = $_SESSION['mahasiswa_id'];
    } else {
        echo "Error: No mahasiswa_id found in session.";
        exit();
    }

    // Debug: Print the data being inserted
    echo "lowongan_id: $lowongan_id<br>";
    echo "mahasiswa_id: $mahasiswa_id<br>";
    echo "pesan: $pesan<br>";
    echo "status: $status<br>";

    // Check if mahasiswa_id exists in the mahasiswas table
    $checkSql = "SELECT COUNT(*) FROM mahasiswas WHERE id = :mahasiswa_id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        echo "Error: mahasiswa_id $mahasiswa_id does not exist in the mahasiswas table.";
        exit();
    }

    // Prepare and bind
    $sql = "INSERT INTO lamaran_mahasiswas (lowongan_id, mahasiswa_id, pesan, status) VALUES (:lowongan_id, :mahasiswa_id, :pesan, :status)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':lowongan_id', $lowongan_id, PDO::PARAM_INT);
    $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $stmt->bindParam(':pesan', $pesan, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    // Execute the statement
    try {
        $stmt->execute();
        echo "New record created successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    header('Location: cari_kerja.php');
    exit;
}
?>
