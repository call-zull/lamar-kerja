<?php
include '../includes/db.php';

if (isset($_GET['user_id'])) {
    $perusahaan_id = $_GET['user_id'];

    try {
        $sql = "SELECT * FROM perusahaans WHERE id = :perusahaan_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['perusahaan_id' => $perusahaan_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // Menambahkan nama jenis perusahaan berdasarkan jenis_perusahaan_id
            switch ($data['jenis_perusahaan_id']) {
                case 1:
                    $data['nama_jenis'] = 'Manufaktur';
                    break;
                case 2:
                    $data['nama_jenis'] = 'Jasa';
                    break;
                case 3:
                    $data['nama_jenis'] = 'Teknologi Informasi dan Komunikasi';
                    break;
                case 4:
                    $data['nama_jenis'] = 'Energi dan Sumber Daya Alam';
                    break;
                case 5:
                    $data['nama_jenis'] = 'Konstruksi dan Real Estate';
                    break;
                case 6:
                    $data['nama_jenis'] = 'Ritel dan Perdagangan';
                    break;
                case 7:
                    $data['nama_jenis'] = 'Agrikultur dan Peternakan';
                    break;
                case 8:
                    $data['nama_jenis'] = 'Transportasi dan Logistik';
                    break;
                case 9:
                    $data['nama_jenis'] = 'Keuangan dan Investasi';
                    break;
                case 10:
                    $data['nama_jenis'] = 'Media dan Hiburan';
                    break;
                case 11:
                    $data['nama_jenis'] = 'Kesehatan dan Bioteknologi';
                    break;
                case 12:
                    $data['nama_jenis'] = 'Pendidikan dan Pelatihan';
                    break;
                default:
                    $data['nama_jenis'] = 'Tidak diketahui';
                    break;
            }

            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
