<?php
include '../../includes/db.php';

if (isset($_POST['id'])) {
    $mahasiswa_id = $_POST['id'];

    try {
        $sql = "SELECT l.*, k.nama_kategori, t.nama
                FROM lomba l
                JOIN kategori k ON l.id_kategori = k.id
                JOIN tingkatan t ON l.id_tingkatan = t.id
                WHERE l.mahasiswa_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mahasiswa_id]);
        $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($competitions)) {
            echo "Tidak ada lomba untuk mahasiswa ini.";
        } else {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead><tr><th>No</th><th>Nama Lomba</th><th>Prestasi</th><th>Kategori</th><th>Tingkatan</th><th>Penyelenggara</th><th>Tanggal Pelaksanaan</th><th>Tempat Pelaksanaan</th><th>Bukti</th></tr></thead><tbody>";

            foreach ($competitions as $index => $competition) {
                $bukti_url = htmlspecialchars(json_decode($competition['bukti'])[0]);
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . htmlspecialchars($competition['nama_lomba']) . "</td>";
                echo "<td>" . htmlspecialchars($competition['prestasi']) . "</td>";
                echo "<td>" . htmlspecialchars($competition['nama_kategori']) . "</td>";
                echo "<td>" . htmlspecialchars($competition['nama']) . "</td>";
                echo "<td>" . htmlspecialchars($competition['penyelenggara']) . "</td>";
                echo "<td>" . htmlspecialchars($competition['tanggal_pelaksanaan']) . "</td>";
                echo "<td>" . htmlspecialchars($competition['tempat_pelaksanaan']) . "</td>";
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
