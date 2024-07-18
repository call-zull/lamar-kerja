<?php
include '../includes/db.php';

if (isset($_GET['lowongan_id'])) {
    $lowongan_id = $_GET['lowongan_id'];

    try {
        // Prepare the SQL statement with placeholders
        $sql = "SELECT lamaran_mahasiswas.*, 
                       mahasiswas.nama_mahasiswa, 
                       mahasiswas.email, 
                       mahasiswas.no_telp, 
                       mahasiswas.profile_image, 
                       mahasiswas.status, 
                       mahasiswas.tahun_masuk, 
                       prodis.nama_prodi
                FROM lamaran_mahasiswas
                LEFT JOIN mahasiswas ON lamaran_mahasiswas.mahasiswa_id = mahasiswas.id
                LEFT JOIN prodis ON mahasiswas.prodi_id = prodis.id 
                WHERE lamaran_mahasiswas.lowongan_id = :lowongan_id";
        
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        
        // Execute the statement with the parameter
        $stmt->execute(['lowongan_id' => $lowongan_id]);
        
        // Fetch all results
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<div style='overflow-x:auto;'><table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelamar</th>
                        <th>Email</th>
                        <th>No Telepon</th>
                        <th>Program Studi</th>
                        <th>Status</th>
                        <th>Tahun Masuk</th>
                        <th>Pesan</th>
                        <th>Profile Image</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>";

        $no = 1;
        foreach ($applicants as $applicant) {
            $profileImage = htmlspecialchars($applicant['profile_image']);
            $profileImagePath = "../path/to/profile/images/" . ($profileImage ? $profileImage : "default.png");

            echo "<tr>
                    <td>" . htmlspecialchars($no) . "</td>
                    <td>" . htmlspecialchars($applicant['nama_mahasiswa']) . "</td>
                    <td>" . htmlspecialchars($applicant['email']) . "</td>
                    <td>" . htmlspecialchars($applicant['no_telp']) . "</td>
                    <td>" . htmlspecialchars($applicant['nama_prodi']) . "</td>
                    <td>" . htmlspecialchars($applicant['status']) . "</td>
                    <td>" . htmlspecialchars($applicant['tahun_masuk']) . "</td>
                    <td>" . htmlspecialchars($applicant['pesan']) . "</td>
                    <td><img src='" . $profileImagePath . "' width='50' height='50' alt='Profile Image'></td>
                    <td>
                        <button class='btn btn-sm btn-info btn-lihat-portofolio' data-mahasiswa-id='" . htmlspecialchars($applicant['mahasiswa_id']) . "' data-toggle='modal' data-target='#modalPortofolio'>Lihat Portofolio</button>
                        <button class='btn btn-sm btn-success btn-terima' data-id-pelamar='" . htmlspecialchars($applicant['id']) . "'>Terima</button>
                        <button class='btn btn-sm btn-danger btn-tolak' data-id-pelamar='" . htmlspecialchars($applicant['id']) . "'>Tolak</button>
                    </td>
                </tr>";
            $no++;
        }

        echo "</tbody></table></div>";

    } catch (PDOException $e) {
        echo "Error fetching applicants: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
