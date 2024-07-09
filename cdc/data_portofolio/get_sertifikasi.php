<?php
include '../../includes/db.php';

if (isset($_POST['id'])) {
    $mahasiswa_id = $_POST['id'];

    try {
        $sql = "SELECT s.*, l.nama_lembaga 
                FROM sertifikasi s
                JOIN penyelenggara_sertifikasi l ON s.lembaga_id = l.id
                WHERE s.mahasiswa_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mahasiswa_id]);
        $certifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($certifications)) {
            echo "Tidak ada sertifikasi untuk mahasiswa ini.";
        } else {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead><tr><th>No</th><th>Nama Sertifikasi</th><th>Nomor SK</th><th>Lembaga</th><th>Tanggal Diperoleh</th><th>Tanggal Kadaluarsa</th><th>Bukti</th></tr></thead><tbody>";

            foreach ($certifications as $index => $certification) {
                // Pastikan URL dibentuk dengan benar
                $bukti_url = htmlspecialchars(json_decode($certification['bukti'])[0]);
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . htmlspecialchars($certification['nama_sertifikasi']) . "</td>";
                echo "<td>" . htmlspecialchars($certification['nomor_sk']) . "</td>";
                echo "<td>" . htmlspecialchars($certification['nama_lembaga']) . "</td>"; // Ganti lembaga_id dengan nama_lembaga
                echo "<td>" . htmlspecialchars($certification['tanggal_diperoleh']) . "</td>";
                echo "<td>" . htmlspecialchars($certification['tanggal_kadaluarsa']) . "</td>";
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
