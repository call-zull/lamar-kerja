<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Function to check if prodi_id is valid
function isValidProdiId($pdo, $prodi_id) {
    $sql = "SELECT COUNT(*) FROM prodis WHERE id = :prodi_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':prodi_id', $prodi_id);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Function to add a job
function addJob($pdo, $nama_pekerjaan, $posisi, $kualifikasi, $prodi_ids, $keahlian) {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        $keahlian_json = json_encode($keahlian);

        $sql = "INSERT INTO lowongan_kerja (nama_pekerjaan, posisi, kualifikasi, keahlian, tanggal_posting) 
                VALUES (:nama_pekerjaan, :posisi, :kualifikasi, :keahlian, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nama_pekerjaan', $nama_pekerjaan);
        $stmt->bindParam(':posisi', $posisi);
        $stmt->bindParam(':kualifikasi', $kualifikasi);
        $stmt->bindParam(':keahlian', $keahlian_json);
        $stmt->execute();
        
        // Get the last inserted ID
        $lowongan_kerja_id = $pdo->lastInsertId();
        
        // Insert each prodi_id into the junction table if valid
        $sql = "INSERT INTO lowongan_kerja_prodi (lowongan_kerja_id, prodi_id) VALUES (:lowongan_kerja_id, :prodi_id)";
        $stmt = $pdo->prepare($sql);
        foreach ($prodi_ids as $prodi_id) {
            if (isValidProdiId($pdo, $prodi_id)) {
                $stmt->bindParam(':lowongan_kerja_id', $lowongan_kerja_id);
                $stmt->bindParam(':prodi_id', $prodi_id);
                $stmt->execute();
            } else {
                throw new Exception("Invalid prodi_id: $prodi_id");
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "Error adding job: " . $e->getMessage();
        return false;
    }
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pekerjaan = $_POST['nama_pekerjaan'];
    $posisi = $_POST['posisi'];
    $kualifikasi = $_POST['kualifikasi'];
    $prodi_ids = $_POST['prodi_id']; // Ensure this is an array
    $keahlian = json_decode($_POST['keahlian'], true); // Decode the JSON string

    // Add job to database
    if (addJob($pdo, $nama_pekerjaan, $posisi, $kualifikasi, $prodi_ids, $keahlian)) {
        header('Location: lowongan_kerja.php');
        exit;
    }
}

// Function to fetch all jobs from the database
function fetchJobs($pdo) {
    try {
        $sql = "SELECT lowongan_kerja.*, GROUP_CONCAT(prodis.nama_prodi SEPARATOR ', ') AS nama_prodi 
                FROM lowongan_kerja 
                LEFT JOIN lowongan_kerja_prodi ON lowongan_kerja.id = lowongan_kerja_prodi.lowongan_kerja_id
                LEFT JOIN prodis ON lowongan_kerja_prodi.prodi_id = prodis.id
                GROUP BY lowongan_kerja.id";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching jobs: " . $e->getMessage();
        return false;
    }
}

// Fetch all jobs
$jobs = fetchJobs($pdo);
?>
