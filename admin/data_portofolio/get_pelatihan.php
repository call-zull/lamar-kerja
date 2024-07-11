<?php
include '../../includes/db.php';

if (isset($_POST['id'])) {
    $mahasiswa_id = $_POST['id'];

    try {
        $sql = "SELECT p.*, t.nama 
                FROM pelatihan p
                JOIN tingkatan t ON p.id_tingkatan = t.id
                WHERE p.mahasiswa_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mahasiswa_id]);
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($trainings)) {
            echo "Tidak ada pelatihan untuk mahasiswa ini.";
        } else {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead><tr><th>No</th><th>Nama Pelatihan</th><th>Materi</th><th>Deskripsi</th><th>Tingkatan</th><th>Penyelenggara</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Tempat Pelaksanaan</th><th>Bukti</th></tr></thead><tbody>";

            foreach ($trainings as $index => $training) {
                $bukti_url = htmlspecialchars(json_decode($training['bukti'])[0]);
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . htmlspecialchars($training['nama_pelatihan']) . "</td>";
                echo "<td>" . htmlspecialchars($training['materi']) . "</td>";
                echo "<td>" . htmlspecialchars($training['deskripsi']) . "</td>";
                echo "<td>" . htmlspecialchars($training['nama']) . "</td>";
                echo "<td>" . htmlspecialchars($training['penyelenggara']) . "</td>"; // Menggunakan kolom penyelenggara langsung dari tabel pelatihan
                echo "<td>" . htmlspecialchars($training['tanggal_mulai']) . "</td>";
                echo "<td>" . htmlspecialchars($training['tanggal_selesai']) . "</td>";
                echo "<td>" . htmlspecialchars($training['tempat_pelaksanaan']) . "</td>";
                echo "<td><a href=\"" . $bukti_url . "\" target=\"_blank\">Lihat Bukti</a></td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
}
?>
