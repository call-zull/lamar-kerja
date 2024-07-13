<?php
include '../includes/db.php';

if (isset($_GET['lowongan_id'])) {
    $lowongan_id = $_GET['lowongan_id'];

    try {
        $sql = "SELECT lamaran_mahasiswas.*, prodis.nama_prodi FROM lamaran_mahasiswas
                LEFT JOIN prodis ON pelamar.prodi_id = prodis.id 
                WHERE pelamar.lowongan_id = :lowongan_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['lowongan_id' => $lowongan_id]);
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($applicants) {
            $no = 1;
            foreach ($applicants as $applicant) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . htmlspecialchars($applicant['nama']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['email']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['no_telepon']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['nama_prodi']) . "</td>";
                echo "<td>
                        <button class='btn btn-sm btn-info'>Detail</button>
                    </td>";
                echo "</tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='6'>Tidak ada pelamar</td></tr>";
        }
    } catch (PDOException $e) {
        echo "<tr><td colspan='6'>Error fetching applicants: " . $e->getMessage() . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='6'>Invalid request</td></tr>";
}
?>
