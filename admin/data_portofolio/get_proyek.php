<?php
include '../../includes/db.php';

if (isset($_POST['id'])) {
    $mahasiswa_id = $_POST['id'];

    try {
        // Fetch student details
        $student_sql = "SELECT m.nim, m.nama_mahasiswa, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.tahun_masuk, m.status
                        FROM mahasiswas m
                        JOIN prodis p ON m.prodi_id = p.id
                        JOIN jurusans j ON m.jurusan_id = j.id
                        WHERE m.id = ?";
        $student_stmt = $pdo->prepare($student_sql);
        $student_stmt->execute([$mahasiswa_id]);
        $student = $student_stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch projects
        $project_sql = "SELECT * FROM proyek WHERE mahasiswa_id = ?";
        $project_stmt = $pdo->prepare($project_sql);
        $project_stmt->execute([$mahasiswa_id]);
        $projects = $project_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($projects)) {
            echo "Tidak ada proyek untuk mahasiswa ini.";
        } else {
            echo "<div id='reportContent' style='font-family: Arial, sans-serif;'>
                    <div style='display: flex; align-items: center; margin-bottom: 20px;'>
                        <img src='../../assets/images/logo_poliban.png' alt='Logo Poliban' style='height: 160px; margin-right: 80px;'>
                        <div style='text-align: center; flex-grow: 1;'>
                            <h5 style='margin: 0;'>KEMENTRIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h5>
                            <h5 style='margin: 0;'>POLITEKNIK NEGERI BANJARMASIN</h5>
                            <h5 style='margin: 0;'>JURUSAN TEKNIK ELEKTRO</h5>
                            <p style='font-size: 12px; margin: 0;'>Jl. Brigjen H. Hasan Basri (Komplek Unlam) Kayutangi, Banjarmasin 70123</p>
                            <p style='font-size: 12px; margin: 0;'>Telp: (0511) 3305052, 3308245, Fax: (0511-3308244, 3308245)</p>
                            <p style='font-size: 12px; margin: 0;'>E-mail: elektro@poliban.ac.id</p>
                        </div>
                    </div>
                    <hr style='border-top: 2px solid #000;'>
                    <h5 style='text-align: center; margin-top: 20px; margin-bottom: 20px;'>LAPORAN PROYEK MAHASISWA</h5>
                    <table style='width: 100%; margin-bottom: 20px;'>
                        <tr>
                            <td style='width: 20%;'><strong>NIM</strong></td>
                            <td style='width: 1%;'>:</td>
                            <td>" . htmlspecialchars($student['nim']) . "</td>
                        </tr>
                        <tr>
                            <td style='width: 20%;'><strong>Nama Lengkap</strong></td>
                            <td style='width: 1%;'>:</td>
                            <td>" . htmlspecialchars($student['nama_mahasiswa']) . "</td>
                        </tr>
                        <tr>
                            <td style='width: 20%;'><strong>Jurusan</strong></td>
                            <td style='width: 1%;'>:</td>
                            <td>" . htmlspecialchars($student['jurusan']) . "</td>
                        </tr>
                        <tr>
                            <td style='width: 20%;'><strong>Prodi</strong></td>
                            <td style='width: 1%;'>:</td>
                            <td>" . htmlspecialchars($student['prodi']) . "</td>
                        </tr>
                        <tr>
                            <td style='width: 20%;'><strong>Tahun Masuk</strong></td>
                            <td style='width: 1%;'>:</td>
                            <td>" . htmlspecialchars($student['tahun_masuk']) . "</td>
                        </tr>
                        <tr>
                            <td style='width: 20%;'><strong>Status</strong></td>
                            <td style='width: 1%;'>:</td>
                            <td>" . htmlspecialchars($student['status']) . "</td>
                        </tr>
                    </table>
                    <table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Proyek</th>
                                <th>Partner</th>
                                <th>Peran</th>
                                <th>Waktu Awal</th>
                                <th>Waktu Selesai</th>
                                <th>Tujuan Proyek</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody>";

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

            echo "</tbody></table>
                </div>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
}
?>
