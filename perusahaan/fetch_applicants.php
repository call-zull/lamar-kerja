<?php
include '../includes/db.php';

if (isset($_GET['lowongan_id'])) {
    $lowongan_id = $_GET['lowongan_id'];

    try {
        // Prepare the SQL statement with placeholders
        $sql = "SELECT lamaran_mahasiswas.*, mahasiswas.*, prodis.nama_prodi, lowongan_kerja.nama_pekerjaan, lowongan_kerja.posisi, lowongan_kerja.kualifikasi, lowongan_kerja.tanggal_posting, lowongan_kerja.batas_waktu
                FROM lamaran_mahasiswas
                LEFT JOIN mahasiswas ON lamaran_mahasiswas.mahasiswa_id = mahasiswas.user_id
                LEFT JOIN prodis ON mahasiswas.prodi_id = prodis.id 
                LEFT JOIN lowongan_kerja ON lamaran_mahasiswas.lowongan_id = lowongan_kerja.id
                WHERE lamaran_mahasiswas.lowongan_id = :lowongan_id";
        
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        
        // Execute the statement with the parameter
        $stmt->execute(['lowongan_id' => $lowongan_id]);
        
        // Fetch all results
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($applicants) {
            $jobDetails = $applicants[0]; // Fetching job details from the first row assuming all applicants share the same job details
            
            echo "<h2>Informasi Lowongan:</h2>";
            echo "<p><strong>Nama Pekerjaan:</strong> " . htmlspecialchars($jobDetails['nama_pekerjaan']) . "</p>";
            echo "<p><strong>Posisi:</strong> " . htmlspecialchars($jobDetails['posisi']) . "</p>";
            echo "<p><strong>Kualifikasi:</strong> " . htmlspecialchars($jobDetails['kualifikasi']) . "</p>";
            echo "<p><strong>Tanggal Posting:</strong> " . htmlspecialchars($jobDetails['tanggal_posting']) . "</p>";
            echo "<p><strong>Batas Waktu:</strong> " . htmlspecialchars($jobDetails['batas_waktu']) . "</p>";

            echo "<table border='1'>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelamar</th>
                            <th>Email</th>
                            <th>No Telepon</th>
                            <th>Program Studi</th>
                            <th>Pesan</th>
                            <th>Profile Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

            $no = 1;
            foreach ($applicants as $applicant) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($no) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['nama_mahasiswa']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['email']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['no_telp']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['nama_prodi']) . "</td>";
                echo "<td>" . htmlspecialchars($applicant['pesan']) . "</td>";
                echo "<td><img src='../path/to/profile/images/" . htmlspecialchars($applicant['profile_image']) . "' width='50' height='50'></td>";
                echo "<td>
                        <button class='btn btn-sm btn-info btn-lihat-portofolio' data-mahasiswa-id='" . htmlspecialchars($applicant['mahasiswa_id']) . "' data-toggle='modal' data-target='#modalPortofolio'>Lihat Portofolio</button>
                        <button class='btn btn-sm btn-success btn-terima' data-mahasiswa-id='" . htmlspecialchars($applicant['mahasiswa_id']) . "'>Terima</button>
                        <button class='btn btn-sm btn-danger btn-tolak' data-mahasiswa-id='" . htmlspecialchars($applicant['mahasiswa_id']) . "'>Tolak</button>
                    </td>";
                echo "</tr>";
                $no++;
            }

            echo "</tbody></table>";
        } else {
            echo "<p>Tidak ada pelamar untuk lowongan ini.</p>";
        }
    } catch (PDOException $e) {
        // Display error message
        echo "Error fetching applicants: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
