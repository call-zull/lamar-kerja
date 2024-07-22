<?php
include '../includes/db.php';

if (isset($_GET['lowongan_id'])) {
    $lowongan_id = $_GET['lowongan_id'];

    $sql = "SELECT lm.mahasiswa_id, m.nama_mahasiswa, m.email, m.no_telp, p.nama_prodi, lm.status, lm.pesan
            FROM lamaran_mahasiswas lm
            JOIN mahasiswas m ON lm.mahasiswa_id = m.id
            JOIN prodis p ON m.prodi_id = p.id
            WHERE lm.lowongan_id = :lowongan_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['lowongan_id' => $lowongan_id]);

    $pelamar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($pelamar) {
        $no = 1;
        foreach ($pelamar as $row) {
            // Only show pelamar who are not accepted or rejected
            if ($row['status'] != 'diterima' && $row['status'] != 'ditolak') {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_mahasiswa']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['no_telp']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_prodi']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pesan']) . "</td>";
                echo "<td>
                        <button class='btn btn-sm btn-info' onclick=\"window.location.href='lihat_pelamar.php?lowongan_id={$lowongan_id}&mahasiswa_id={$row['mahasiswa_id']}'\">Lihat Portofolio</button>
                    </td>";
                echo "</tr>";
                $no++;
            }
        }
    } else {
        echo "<tr><td colspan='8'>Belum ada pelamar untuk lowongan ini.</td></tr>";
    }
}
?>
