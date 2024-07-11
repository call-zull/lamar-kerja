<?php
require_once __DIR__ . '/includes/db.php';

function getLatestPortfolios($pdo, $search = null) {
    $sql = "
        SELECT 'sertifikasi' AS type, s.id, s.nama_sertifikasi AS title, s.tanggal_diperoleh AS date, s.bukti AS evidence, 
               m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p1.nama_prodi AS prodi,
               m.jk, m.alamat, m.tahun_masuk, m.status, m.email
        FROM sertifikasi s
        JOIN mahasiswas m ON s.mahasiswa_id = m.id
        JOIN jurusans j ON m.jurusan_id = j.id
        JOIN prodis p1 ON m.prodi_id = p1.id
        WHERE 1
        UNION
        SELECT 'lomba' AS type, l.id, l.nama_lomba AS title, l.tanggal_pelaksanaan AS date, l.bukti AS evidence, 
               m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p2.nama_prodi AS prodi,
               m.jk, m.alamat, m.tahun_masuk, m.status, m.email
        FROM lomba l
        JOIN mahasiswas m ON l.mahasiswa_id = m.id
        JOIN jurusans j ON m.jurusan_id = j.id
        JOIN prodis p2 ON m.prodi_id = p2.id
        WHERE 1
        UNION
        SELECT 'pelatihan' AS type, p3.id_pelatihan AS id, p3.nama_pelatihan AS title, p3.tanggal_mulai AS date, p3.bukti AS evidence, 
               m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p3_prodi.nama_prodi AS prodi,
               m.jk, m.alamat, m.tahun_masuk, m.status, m.email
        FROM pelatihan p3
        JOIN mahasiswas m ON p3.mahasiswa_id = m.id
        JOIN jurusans j ON m.jurusan_id = j.id
        JOIN prodis p3_prodi ON m.prodi_id = p3_prodi.id
        WHERE 1
        UNION
        SELECT 'proyek' AS type, p4.id AS id, p4.nama_proyek AS title, p4.waktu_awal AS date, p4.bukti AS evidence, 
               m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p4_prodi.nama_prodi AS prodi,
               m.jk, m.alamat, m.tahun_masuk, m.status, m.email
        FROM proyek p4
        JOIN mahasiswas m ON p4.mahasiswa_id = m.id
        JOIN jurusans j ON m.jurusan_id = j.id
        JOIN prodis p4_prodi ON m.prodi_id = p4_prodi.id
        WHERE 1";

    if ($search) {
        $searchCondition = " AND (p1.nama_prodi LIKE :search OR p2.nama_prodi LIKE :search OR p3_prodi.nama_prodi LIKE :search OR p4_prodi.nama_prodi LIKE :search
                                 OR s.nama_sertifikasi LIKE :search OR l.nama_lomba LIKE :search OR p3.nama_pelatihan LIKE :search OR p4.nama_proyek LIKE :search)";
        $sql .= $searchCondition;
    }

    $sql .= " ORDER BY date DESC
              LIMIT 20"; // Adjust the limit as needed

    $stmt = $pdo->prepare($sql);

    if ($search) {
        $search = "%$search%";
        $stmt->bindParam(':search', $search);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$search = isset($_GET['search']) ? $_GET['search'] : null;

$portfolios = getLatestPortfolios($pdo, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliban Mahasiswa Portofolio</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-lg {
            max-width: 90%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="assets/images/logo_poliban.png" alt="Poliban Logo" width="30" height="30" class="d-inline-block align-top">
                SI Portofolio
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="teknikSipilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teknik Sipil dan Kebumian
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teknikSipilDropdown">
                            <li><a class="dropdown-item" href="teknik_sipil_dan_kebumian/D4_Sarjana_Terapan_Teknik_Bangunan_Rawa.php">D4 Sarjana Terapan Teknik Bangunan Rawa</a></li>
                            <li><a class="dropdown-item" href="teknik_sipil_dan_kebumian/D4_Sarjana_Terapan_Teknologi_Rekayasa_Geomatika.php">D4 Sarjana Terapan Teknologi Rekayasa Geomatika</a></li>
                            <li><a class="dropdown-item" href="teknik_sipil_dan_kebumian/D4_Sarjana_Terapan_Teknologi_Rekayasa_Konstruksi_Jalan.php">D4 Sarjana Terapan Teknologi Rekayasa Konstruksi Jalan</a></li>
                            <li><a class="dropdown-item" href="teknik_sipil_dan_kebumian/D3_Teknik_Sipil.php">D3 Teknik Sipil</a></li>
                            <li><a class="dropdown-item" href="teknik_sipil_dan_kebumian/D3_Teknik_Pertambangan.php">D3 Teknik Pertambangan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="teknikMesinDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teknik Mesin
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teknikMesinDropdown">
                            <li><a class="dropdown-item" href="teknik_mesin/D4_Sarjana_Terapan_Teknologi_Rekayasa_Otomotif.php">D4 Sarjana Terapan Teknologi Rekayasa Otomotif</a></li>
                            <li><a class="dropdown-item" href="teknik_mesin/D3_Alat_Berat.php">D3 Alat Berat</a></li>
                            <li><a class="dropdown-item" href="teknik_mesin/D3_Teknik_Mesin.php">D3 Teknik Mesin</a></li>
                            <li><a class="dropdown-item" href="teknik_mesin/D2_Fast_Track_Tata_Operasi_dan_Pemeliharaan_Prediktif.php">D2 Fast Track Tata Operasi dan Pemeliharaan Prediktif</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="administrasiBisnisDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Administrasi Bisnis
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="administrasiBisnisDropdown">
                            <li><a class="dropdown-item" href="administrasi_bisnis/D4_Sarjana_Terapan_Bisnis_Digital.php">D4 Sarjana Terapan Bisnis Digital</a></li>
                            <li><a class="dropdown-item" href="administrasi_bisnis/D3_Administrasi_Bisnis.php">D3 Administrasi Bisnis</a></li>
                            <li><a class="dropdown-item" href="administrasi_bisnis/D3_Manajemen_Informatika.php">D3 Manajemen Informatika</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="teknikElektroDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teknik Elektro
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teknikElektroDropdown">
                            <li><a class="dropdown-item" href="teknik_elektro/D4_Sarjana_Terapan_Teknologi_Rekayasa_Pembangkit_Energi.php">D4 Sarjana Terapan Teknologi Rekayasa Pembangkit Energi</a></li>
                            <li><a class="dropdown-item" href="teknik_elektro/D4_Sarjana_Terapan_Sistem_Informasi_Kota_Cerdas.php">D4 Sarjana Terapan Sistem Informasi Kota Cerdas</a></li>
                            <li><a class="dropdown-item" href="teknik_elektro/D4_Sarjana_Terapan_Teknologi_Rekayasa_Otomasi.php">D4 Sarjana Terapan Teknologi Rekayasa Otomasi</a></li>
                            <li><a class="dropdown-item" href="teknik_elektro/D3_Teknik_Listrik.php">D3 Teknik Listrik</a></li>
                            <li><a class="dropdown-item" href="teknik_elektro/D3_Teknik_Informatika.php">D3 Teknik Informatika</a></li>
                            <li><a class="dropdown-item" href="teknik_elektro/D3_Elektronika.php">D3 Elektronika</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="akuntansiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Akuntansi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="akuntansiDropdown">
                            <li><a class="dropdown-item" href="akuntansi/D4_Sarjana_Terapan_Akuntansi_Lembaga_Keuangan_Syariah.php">D4 Sarjana Terapan Akuntansi Lembaga Keuangan Syariah</a></li>
                            <li><a class="dropdown-item" href="akuntansi/D3_Akuntansi.php">D3 Akuntansi</a></li>
                            <li><a class="dropdown-item" href="akuntansi/D3_Komputerisasi_Akuntansi.php">D3 Komputerisasi Akuntansi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link web-poliban" href="https://www.poliban.ac.id/">Web Poliban</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link register" href="auth/register.php">Register</a></li>
                    <li class="nav-item">
                        <a class="nav-link login" href="auth/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col text-center">
                <h2>Portofolio Mahasiswa Poliban</h2>
                <p class="lead">Kumpulan karya terbaik dari mahasiswa dan alumni Poliban</p>
            </div>
        </div>

        <!-- Carousel -->
        <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/images/poliban_1.jpeg" class="d-block mx-auto w-50" alt="..." style="max-height: 200px;">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/poliban_4.png" class="d-block mx-auto w-50" alt="..." style="max-height: 200px;">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/poliban_3.jpg" class="d-block mx-auto w-50" alt="..." style="max-height: 200px;">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <!-- Search Form -->
        <form method="GET" action="index.php" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan prodi dan keahlian" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </div>
        </form>

        <div class="row" id="portfolio-list">
        <?php if (empty($portfolios)): ?>
            <div class="col text-center">
                <p class="lead">Tidak ada data terkait.</p>
            </div>
        <?php else: ?>
            <?php foreach ($portfolios as $portfolio): ?>
                <div class="col-md-4 portfolio-item" data-jurusan="<?= htmlspecialchars($portfolio['jurusan']) ?>" data-prodi="<?= htmlspecialchars($portfolio['prodi']) ?>">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body d-flex">
                            <img src="assets/mahasiswa/profile/<?= htmlspecialchars($portfolio['profile_image']) ?>" class="card-img-left" alt="Profile <?= htmlspecialchars($portfolio['nama_mahasiswa']) ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
                            <div class="details">
                                <h5 class="card-title"><?= htmlspecialchars($portfolio['nama_mahasiswa']) ?></h5>
                                <p class="card-text">
                                    NIM: <?= htmlspecialchars($portfolio['nim']) ?><br>
                                    Jurusan: <?= htmlspecialchars($portfolio['jurusan']) ?><br>
                                    Prodi: <?= htmlspecialchars($portfolio['prodi']) ?><br>
                                    Keahlian: <?= htmlspecialchars($portfolio['title']) ?><br>
                                </p>
                                <div class="row">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <button type="button" class="btn btn-success more" data-bs-toggle="modal" data-bs-target="#modal<?= $portfolio['id'] ?>">Selengkapnya</button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-primary portfolio" data-type="<?= htmlspecialchars($portfolio['type']) ?>" data-id="<?= htmlspecialchars($portfolio['id']) ?>" data-bs-toggle="modal" data-bs-target="#portfolioModal">Portofolio</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="modal<?= $portfolio['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $portfolio['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel<?= $portfolio['id'] ?>">Detail Mahasiswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="assets/mahasiswa/profile/<?= htmlspecialchars($portfolio['profile_image']) ?>" class="img-fluid rounded-circle" alt="Profile <?= htmlspecialchars($portfolio['nama_mahasiswa']) ?>">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="details">
                                                <h5 class="card-title"><?= htmlspecialchars($portfolio['nama_mahasiswa']) ?></h5>
                                                <p class="card-text">
                                                    NIM: <?= htmlspecialchars($portfolio['nim']) ?><br>
                                                    Jurusan: <?= htmlspecialchars($portfolio['jurusan']) ?><br>
                                                    Prodi: <?= htmlspecialchars($portfolio['prodi']) ?><br>
                                                    Jenis Kelamin: <?= htmlspecialchars($portfolio['jk']) ?><br>
                                                    Alamat: <?= htmlspecialchars($portfolio['alamat']) ?><br>
                                                    Tahun Masuk: <?= htmlspecialchars($portfolio['tahun_masuk']) ?><br>
                                                    Status: <?= htmlspecialchars($portfolio['status']) ?><br>
                                                    Email: <?= htmlspecialchars($portfolio['email']) ?><br>
                                                    Keahlian: <?= htmlspecialchars($portfolio['title']) ?><br>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Portofolio -->
    <div class="modal fade" id="portfolioModal" tabindex="-1" aria-labelledby="portfolioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="portfolioModalLabel">Detail Portofolio Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.portfolio').forEach(function (button) {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');

                    fetch(`fetch_portfolio_details.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            const modalTitle = document.querySelector('#portfolioModalLabel');
                            modalTitle.textContent = `Detail Portofolio Mahasiswa, ${data.mahasiswa.nim}`;

                            const modalBody = document.querySelector('#portfolioModal .modal-body');
                            let html = '';

                            html += '<h5>Sertifikasi Mahasiswa</h5>';
                            html += '<div class="table-responsive"><table class="table table-bordered">';
                            html += '<thead><tr><th>No</th><th>Nama Sertifikasi</th><th>Nomor SK</th><th>Lembaga</th><th>Tanggal Diperoleh</th><th>Tanggal Kadaluarsa</th><th>Bukti</th></tr></thead><tbody>';
                            if (data.sertifikasi.length > 0) {
                                data.sertifikasi.forEach((item, index) => {
                                    const bukti = JSON.parse(item.bukti);
                                    const buktiPath = bukti.map(b => `<a href="assets/mahasiswa/sertifikasi/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
                                    html += `<tr>
                                        <td>${index + 1}</td>
                                        <td>${item.nama_sertifikasi}</td>
                                        <td>${item.nomor_sk}</td>
                                        <td>${item.lembaga}</td>
                                        <td>${item.tanggal_diperoleh}</td>
                                        <td>${item.tanggal_kadaluarsa}</td>
                                        <td>${buktiPath}</td>
                                    </tr>`;
                                });
                            } else {
                                html += '<tr><td colspan="7" style="text-align: center;">Tidak ada data sertifikasi yang tersedia.</td></tr>';
                            }
                            html += '</tbody></table></div>';

                            html += '<h5>Lomba Mahasiswa</h5>';
                            html += '<div class="table-responsive"><table class="table table-bordered">';
                            html += '<thead><tr><th>No</th><th>Nama Lomba</th><th>Prestasi</th><th>Kategori</th><th>Tingkatan</th><th>Penyelenggara</th><th>Tanggal Pelaksanaan</th><th>Tempat Pelaksanaan</th><th>Bukti</th></tr></thead><tbody>';
                            if (data.lomba.length > 0) {
                                data.lomba.forEach((item, index) => {
                                    const bukti = JSON.parse(item.bukti);
                                    const buktiPath = bukti.map(b => `<a href="assets/mahasiswa/lomba/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
                                    html += `<tr>
                                        <td>${index + 1}</td>
                                        <td>${item.nama_lomba}</td>
                                        <td>${item.prestasi}</td>
                                        <td>${item.kategori}</td>
                                        <td>${item.tingkatan}</td>
                                        <td>${item.penyelenggara}</td>
                                        <td>${item.tanggal_pelaksanaan}</td>
                                        <td>${item.tempat_pelaksanaan}</td>
                                        <td>${buktiPath}</td>
                                    </tr>`;
                                });
                            } else {
                                html += '<tr><td colspan="9" style="text-align: center;">Tidak ada data lomba yang tersedia.</td></tr>';
                            }
                            html += '</tbody></table></div>';

                            html += '<h5>Pelatihan Mahasiswa</h5>';
                            html += '<div class="table-responsive"><table class="table table-bordered">';
                            html += '<thead><tr><th>No</th><th>Nama Pelatihan</th><th>Materi</th><th>Deskripsi</th><th>Tingkatan</th><th>Penyelenggara</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Tempat Pelaksanaan</th><th>Bukti</th></tr></thead><tbody>';
                            if (data.pelatihan.length > 0) {
                                data.pelatihan.forEach((item, index) => {
                                    const bukti = JSON.parse(item.bukti);
                                    const buktiPath = bukti.map(b => `<a href="assets/mahasiswa/pelatihan/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
                                    html += `<tr>
                                        <td>${index + 1}</td>
                                        <td>${item.nama_pelatihan}</td>
                                        <td>${item.materi}</td>
                                        <td>${item.deskripsi}</td>
                                        <td>${item.tingkatan}</td>
                                        <td>${item.penyelenggara}</td>
                                        <td>${item.tanggal_mulai}</td>
                                        <td>${item.tanggal_selesai}</td>
                                        <td>${item.tempat_pelaksanaan}</td>
                                        <td>${buktiPath}</td>
                                    </tr>`;
                                });
                            } else {
                                html += '<tr><td colspan="10" style="text-align: center;">Tidak ada data pelatihan yang tersedia.</td></tr>';
                            }
                            html += '</tbody></table></div>';

                            html += '<h5>Proyek Mahasiswa</h5>';
                            html += '<div class="table-responsive"><table class="table table-bordered">';
                            html += '<thead><tr><th>No</th><th>Nama Proyek</th><th>Partner</th><th>Peran</th><th>Waktu Awal</th><th>Waktu Selesai</th><th>Tujuan Proyek</th><th>Bukti</th></tr></thead><tbody>';
                            if (data.proyek.length > 0) {
                                data.proyek.forEach((item, index) => {
                                    const bukti = JSON.parse(item.bukti);
                                    const buktiPath = bukti.map(b => `<a href="assets/mahasiswa/proyek/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
                                    html += `<tr>
                                        <td>${index + 1}</td>
                                        <td>${item.nama_proyek}</td>
                                        <td>${item.partner}</td>
                                        <td>${item.peran}</td>
                                        <td>${item.waktu_awal}</td>
                                        <td>${item.waktu_selesai}</td>
                                        <td>${item.tujuan_proyek}</td>
                                        <td>${buktiPath}</td>
                                    </tr>`;
                                });
                            } else {
                                html += '<tr><td colspan="8" style="text-align: center;">Tidak ada data proyek yang tersedia.</td></tr>';
                            }
                            html += '</tbody></table></div>';

                            modalBody.innerHTML = html;
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });

    </script>
</body>
</html>
