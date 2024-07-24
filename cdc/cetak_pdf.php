<?php
require '../vendor/autoload.php'; // Correct path to autoload.php
include '../includes/db.php';

use Mpdf\Mpdf;

if (isset($_GET['id'])) {
    $mahasiswa_id = $_GET['id'];

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

        // Fetch certifications
        $certification_sql = "SELECT s.*, l.nama_lembaga 
                              FROM sertifikasi s
                              JOIN penyelenggara_sertifikasi l ON s.lembaga_id = l.id
                              WHERE s.mahasiswa_id = ?";
        $certification_stmt = $pdo->prepare($certification_sql);
        $certification_stmt->execute([$mahasiswa_id]);
        $certifications = $certification_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch competitions
        $competition_sql = "SELECT l.*, k.nama_kategori, t.nama
                            FROM lomba l
                            JOIN kategori k ON l.id_kategori = k.id
                            JOIN tingkatan t ON l.id_tingkatan = t.id
                            WHERE l.mahasiswa_id = ?";
        $competition_stmt = $pdo->prepare($competition_sql);
        $competition_stmt->execute([$mahasiswa_id]);
        $competitions = $competition_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch trainings
        $training_sql = "SELECT p.*, t.nama
                         FROM pelatihan p
                         JOIN tingkatan t ON p.id_tingkatan = t.id
                         WHERE p.mahasiswa_id = ?";
        $training_stmt = $pdo->prepare($training_sql);
        $training_stmt->execute([$mahasiswa_id]);
        $trainings = $training_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Buat konten HTML untuk PDF
        //<img src="../../assets/images/logo_poliban.png" style="height: 90px; margin-right: 20px;">
        $html = '
        <style>
            .label {
                display: inline-block;
                width: 120px;
            }
            .value {
                display: inline-block;
            }
        </style>
   <div style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: center;">
       
        <div style="text-align: center;">
            <h3 style="margin: 0;">KEMENTRIAN PENDIDIKAN, KEBUDAYAAN,</h3>
            <h3 style="margin: 0;">RISET, DAN TEKNOLOGI</h3>
            <h3 style="margin: 0;">POLITEKNIK NEGERI BANJARMASIN</h3>
            <p style="margin: 0;">Jl. Brigjen H. Hasan Basri (Komplek Unlam) Kayutangi, Banjarmasin 70123</p>
            <p style="margin: 0;">Telp: (0511) 3305052, 3308245, Fax: (0511-3308244, 3308245) E-mail: info@poliban.ac.id</p>
        </div>
    </div>
    <hr style="border: 2px solid black; margin-top: 10px;">
</div>
        <h4 style="text-align: center; margin-top: 10px;">LAPORAN PROYEK MAHASISWA</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td class="label">NIM</td>
                <td class="value">: ' . htmlspecialchars($student['nim']) . '</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="value">: ' . htmlspecialchars($student['nama_mahasiswa']) . '</td>
            </tr>
            <tr>
                <td class="label">Jurusan</td>
                <td class="value">: ' . htmlspecialchars($student['jurusan']) . '</td>
            </tr>
            <tr>
                <td class="label">Prodi</td>
                <td class="value">: ' . htmlspecialchars($student['prodi']) . '</td>
            </tr>
            <tr>
                <td class="label">Tahun Masuk</td>
                <td class="value">: ' . htmlspecialchars($student['tahun_masuk']) . '</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">: ' . htmlspecialchars($student['status']) . '</td>
            </tr>
        </table>
        <hr>
        <h4>Sertifikasi</h4>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black;">No</th>
                    <th style="border: 1px solid black;">Nama Sertifikasi</th>
                    <th style="border: 1px solid black;">Nomor SK</th>
                    <th style="border: 1px solid black;">Lembaga</th>
                    <th style="border: 1px solid black;">Tanggal Diperoleh</th>
                    <th style="border: 1px solid black;">Tanggal Kadaluarsa</th>
                    <th style="border: 1px solid black;">Bukti</th>
                </tr>
            </thead>
            <tbody>';
        if (!empty($certifications)) {
            foreach ($certifications as $index => $certification) {
                $bukti_url = htmlspecialchars(json_decode($certification['bukti'])[0]);
                $html .= '<tr>
                    <td style="border: 1px solid black;">' . ($index + 1) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($certification['nama_sertifikasi']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($certification['nomor_sk']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($certification['nama_lembaga']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($certification['tanggal_diperoleh']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($certification['tanggal_kadaluarsa']) . '</td>
                    <td style="border: 1px solid black;"><a href="' . $bukti_url . '" target="_blank">Lihat Bukti</a></td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="7" style="border: 1px solid black; text-align: center;">Tidak ada sertifikasi yang ditemukan.</td></tr>';
        }
        $html .= '
            </tbody>
        </table>
        <h4>Proyek</h4>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black;">No</th>
                    <th style="border: 1px solid black;">Judul Proyek</th>
                    <th style="border: 1px solid black;">Deskripsi</th>
                    <th style="border: 1px solid black;">Peran</th>
                    <th style="border: 1px solid black;">Tanggal Mulai</th>
                    <th style="border: 1px solid black;">Tanggal Selesai</th>
                    <th style="border: 1px solid black;">Bukti</th>
                </tr>
            </thead>
            <tbody>';
        if (!empty($projects)) {
            foreach ($projects as $index => $project) {
                $bukti_url = htmlspecialchars(json_decode($project['bukti'])[0]);
                $html .= '<tr>
                    <td style="border: 1px solid black;">' . ($index + 1) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($project['judul_proyek']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($project['deskripsi']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($project['peran']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($project['tanggal_mulai']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($project['tanggal_selesai']) . '</td>
                    <td style="border: 1px solid black;"><a href="' . $bukti_url . '" target="_blank">Lihat Bukti</a></td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="7" style="border: 1px solid black; text-align: center;">Tidak ada proyek yang ditemukan.</td></tr>';
        }
        $html .= '
            </tbody>
        </table>
        <h4>Lomba</h4>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black;">No</th>
                    <th style="border: 1px solid black;">Nama Lomba</th>
                    <th style="border: 1px solid black;">Kategori</th>
                    <th style="border: 1px solid black;">Tingkat</th>
                    <th style="border: 1px solid black;">Tanggal Lomba</th>
                    <th style="border: 1px solid black;">Pencapaian</th>
                    <th style="border: 1px solid black;">Bukti</th>
                </tr>
            </thead>
            <tbody>';
        if (!empty($competitions)) {
            foreach ($competitions as $index => $competition) {
                $bukti_url = htmlspecialchars(json_decode($competition['bukti'])[0]);
                $html .= '<tr>
                    <td style="border: 1px solid black;">' . ($index + 1) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($competition['nama_lomba']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($competition['nama_kategori']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($competition['nama']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($competition['tanggal_lomba']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($competition['pencapaian']) . '</td>
                    <td style="border: 1px solid black;"><a href="' . $bukti_url . '" target="_blank">Lihat Bukti</a></td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="7" style="border: 1px solid black; text-align: center;">Tidak ada lomba yang ditemukan.</td></tr>';
        }
        $html .= '
            </tbody>
        </table>
        <h4>Pelatihan</h4>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black;">No</th>
                    <th style="border: 1px solid black;">Nama Pelatihan</th>
                    <th style="border: 1px solid black;">Tingkat</th>
                    <th style="border: 1px solid black;">Tanggal Mulai</th>
                    <th style="border: 1px solid black;">Tanggal Selesai</th>
                    <th style="border: 1px solid black;">Bukti</th>
                </tr>
            </thead>
            <tbody>';
        if (!empty($trainings)) {
            foreach ($trainings as $index => $training) {
                $bukti_url = htmlspecialchars(json_decode($training['bukti'])[0]);
                $html .= '<tr>
                    <td style="border: 1px solid black;">' . ($index + 1) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($training['nama_pelatihan']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($training['nama']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($training['tanggal_mulai']) . '</td>
                    <td style="border: 1px solid black;">' . htmlspecialchars($training['tanggal_selesai']) . '</td>
                    <td style="border: 1px solid black;"><a href="' . $bukti_url . '" target="_blank">Lihat Bukti</a></td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="7" style="border: 1px solid black; text-align: center;">Tidak ada pelatihan yang ditemukan.</td></tr>';
        }
        $html .= '
            </tbody>
        </table>';

        // Buat objek Mpdf dan cetak PDF
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output('laporan_proyek_mahasiswa.pdf', 'I');
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
} else {
    echo "ID mahasiswa tidak diberikan.";
}