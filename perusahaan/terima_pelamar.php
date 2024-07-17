<?php
include '../includes/db.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pelamar'])) {
    $id_pelamar = $_POST['id_pelamar'];

    try {
        $sql = "UPDATE lamaran_mahasiswas SET status = 'Diterima' WHERE id = :id_pelamar";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_pelamar' => $id_pelamar]);

        $response['success'] = true;
        $response['message'] = 'Pelamar diterima.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>
