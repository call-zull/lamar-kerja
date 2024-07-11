<?php
include '../../includes/db.php';

if (isset($_POST['id'])) {
    $mahasiswa_id = $_POST['id'];

    try {
        $sql = "SELECT p.*
                FROM proyek p
                WHERE p.mahasiswa_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mahasiswa_id]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($projects)) {
            echo "Tidak ada proyek untuk mahasiswa ini.";
        } else {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead><tr><th>No</th><th>Nama Proyek</th><th>Partner</th><th>Peran</th><th>Waktu Awal</th><th>Waktu Selesai</th><th>Tujuan Proyek</th><th>Bukti</th></tr></thead><tbody>";

            foreach ($projects as $index => $project) {
                $bukti_url = htmlspecialchars(json_decode($project['bukti'])[0]);
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . htmlspecialchars($project['nama_proyek']) . "</td>";
                echo "<td>" . htmlspecialchars($project['partner']) . "</td>";
                echo "<td>" . htmlspecialchars($project['peran']) . "</td>";
                echo "<td>" . htmlspecialchars($project['waktu_awal']) . "</td>";
                echo "<td>" . htmlspecialchars($project['waktu_selesai']) . "</td>";
                echo "<td>" . htmlspecialchars($project['tujuan_proyek']) . "</td>";
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
