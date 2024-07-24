<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

$job_id = $_GET['job_id'];
$sql = "SELECT l.*, p.nama_perusahaan, p.alamat_perusahaan, p.email_perusahaan, p.tahun_didirikan, 
        p.pimpinan_perusahaan, p.deskripsi_perusahaan, p.no_telp, jp.nama_jenis 
        FROM lowongan_kerja l
        JOIN perusahaans p ON l.perusahaan_id = p.id
        JOIN jenis_perusahaan jp ON p.jenis_perusahaan_id = jp.id
        WHERE l.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$job_id]);
$job = $stmt->fetch();
if (!$job) {
    header("Location: ./perusahaan_admin.php");
}
// Split the jobdesk string into an array
$jobdesk = array_filter(array_map('trim', explode('-', $job['jobdesk'])));
// Split the kualifikasi string into an array
$kualifikasi = array_filter(array_map('trim', explode('-', $job['kualifikasi'])));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan Kerja</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4>Job Description</h4>
                        <ul>
                            <?php foreach ($jobdesk as $desc) : ?>
                                <li><?= htmlspecialchars($desc) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <h4>Job Requirements</h4>
                        <ul>
                            <?php foreach ($kualifikasi as $req) : ?>
                                <li><?= htmlspecialchars($req) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5><?php echo $job['nama_pekerjaan']?></h5>
                        <p class="text-muted"><?php echo $job['jenis_kerja']?></p>
                        <hr>
                        <p><strong>Nama Perusahaan:</strong> <?php echo $job['nama_perusahaan']?></p>
                        <p><strong>Level Jabatan:</strong> <?php echo $job['posisi']?></p>
                        <p><strong>Bisnis Utama:</strong> <?php echo $job['nama_jenis']?></p>
                        <p><strong>Office:</strong> <?php echo $job['alamat_perusahaan']?></p>
                        <a href="#" class="btn btn-primary">Lamar Lowongan ini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>