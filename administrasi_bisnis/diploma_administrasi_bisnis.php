<?php
require_once __DIR__ . '/../includes/db.php';

function getStudentPortfolios($pdo, $search = null) {
    $queries = [
        "SELECT m.id AS mahasiswa_id, m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.keahlian, 
                m.jk, m.alamat, m.tahun_masuk, m.status, m.email, 'sertifikasi' AS type, s.id, s.nama_sertifikasi AS title, s.tanggal_diperoleh AS date, s.bukti AS evidence
         FROM mahasiswas m
         LEFT JOIN sertifikasi s ON s.mahasiswa_id = m.id
         LEFT JOIN jurusans j ON m.jurusan_id = j.id
         LEFT JOIN prodis p ON m.prodi_id = p.id
         WHERE p.nama_prodi = 'D3 Administrasi Bisnis'
         AND (:search IS NULL OR m.keahlian LIKE :search OR s.nama_sertifikasi LIKE :search)",

        "SELECT m.id AS mahasiswa_id, m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.keahlian,
                m.jk, m.alamat, m.tahun_masuk, m.status, m.email, 'lomba' AS type, l.id, l.nama_lomba AS title, l.tanggal_pelaksanaan AS date, l.bukti AS evidence
         FROM mahasiswas m
         LEFT JOIN lomba l ON l.mahasiswa_id = m.id
         LEFT JOIN jurusans j ON m.jurusan_id = j.id
         LEFT JOIN prodis p ON m.prodi_id = p.id
         WHERE p.nama_prodi = 'D3 Administrasi Bisnis'
         AND (:search IS NULL OR m.keahlian LIKE :search OR l.nama_lomba LIKE :search)",

        "SELECT m.id AS mahasiswa_id, m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.keahlian,
                m.jk, m.alamat, m.tahun_masuk, m.status, m.email, 'pelatihan' AS type, p3.id_pelatihan AS id, p3.nama_pelatihan AS title, p3.tanggal_mulai AS date, p3.bukti AS evidence
         FROM mahasiswas m
         LEFT JOIN pelatihan p3 ON p3.mahasiswa_id = m.id
         LEFT JOIN jurusans j ON m.jurusan_id = j.id
         LEFT JOIN prodis p ON m.prodi_id = p.id
         WHERE p.nama_prodi = 'D3 Administrasi Bisnis'
         AND (:search IS NULL OR m.keahlian LIKE :search OR p3.nama_pelatihan LIKE :search)",

        "SELECT m.id AS mahasiswa_id, m.nama_mahasiswa, m.nim, m.profile_image, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.keahlian,
                m.jk, m.alamat, m.tahun_masuk, m.status, m.email, 'proyek' AS type, p4.id AS id, p4.nama_proyek AS title, p4.waktu_awal AS date, p4.bukti AS evidence
         FROM mahasiswas m
         LEFT JOIN proyek p4 ON p4.mahasiswa_id = m.id
         LEFT JOIN jurusans j ON m.jurusan_id = j.id
         LEFT JOIN prodis p ON m.prodi_id = p.id
         WHERE p.nama_prodi = 'D3 Administrasi Bisnis'
         AND (:search IS NULL OR m.keahlian LIKE :search OR p4.nama_proyek LIKE :search)"
    ];

    $results = [];
    foreach ($queries as $query) {
        $stmt = $pdo->prepare($query);
        if ($search) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam);
        } else {
            $stmt->bindValue(':search', null, PDO::PARAM_NULL);
        }
        $stmt->execute();
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    $portfolios = [];
    foreach ($results as $result) {
        $id = $result['mahasiswa_id'];
        if (!isset($portfolios[$id])) {
            $portfolios[$id] = [
                'mahasiswa_id' => $result['mahasiswa_id'],
                'nama_mahasiswa' => $result['nama_mahasiswa'],
                'nim' => $result['nim'],
                'profile_image' => $result['profile_image'],
                'jurusan' => $result['jurusan'],
                'prodi' => $result['prodi'],
                'keahlian' => $result['keahlian'],
                'jk' => $result['jk'],
                'alamat' => $result['alamat'],
                'tahun_masuk' => $result['tahun_masuk'],
                'status' => $result['status'],
                'email' => $result['email'],
                'sertifikasi' => [],
                'lomba' => [],
                'pelatihan' => [],
                'proyek' => []
            ];
        }
        $portfolios[$id][$result['type']][] = [
            'id' => $result['id'],
            'title' => $result['title'],
            'date' => $result['date'],
            'evidence' => $result['evidence']
        ];
    }

    return $portfolios;
}

$search = isset($_GET['search']) ? $_GET['search'] : null;
$portfolios = getStudentPortfolios($pdo, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Mahasiswa Jurusan Administrasi Bisnis</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
.section-title {
            background-color: #f8f9fa; /* Light gray background */
            padding: 10px;
            border-bottom: 1px solid #dee2e6; /* Border below the title */
        }
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
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title {
            font-weight: bold;
        }
        .modal-content {
            padding: 20px;
        }
        .modal-body h6 {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #343a40;
        }

        .modal-body .card-text strong {
            color: #495057;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .skill {
            background-color: #add8e6;
            color: #000;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .img-fluid {
                margin: 0 auto 15px;
            }
            .modal-body .col-md-4,
            .modal-body .col-md-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        .list-group-item {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }

        .img-fluid {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        body {
            padding-top: 70px; /* To ensure the content doesn't hide under the navbar */
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .social-icons a {
            margin-right: 10px;
            font-size: 30px;
            text-decoration: none;
        }
        .social-icons .fa-facebook-f {
            color: #3b5998;
        }
        .social-icons .fa-twitter {
            color: #00acee;
        }
        .social-icons .fa-instagram {
            color: #C13584;
        }
        .social-icons .fa-youtube {
            color: #FF0000;
        }
        .footer {
            background-color: #f8f9fa; /* Light gray background */
            padding: 20px 0;
        }
        .footer .footer-col {
            margin-bottom: 10px;
        }
        .footer img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }
        .footer .social-icons a {
            margin-right: 5px;
        }
        .footer .social-icons img {
            width: 20px;
            height: 20px;
        }
        .footer h4 {
            margin-bottom: 10px;
        }
        .map-container {
            text-align: center;
            margin-top: 20px;
        }
        .map-container iframe {
            width: 500px;
            height: 200px;
            border: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="https://www.poliban.ac.id/">
                <img src="../assets/images/logo_poliban.png" alt="Poliban Logo" width="30" height="30" class="d-inline-block align-top">
                SI Portofolio
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" idteknikSipilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teknik Sipil dan Kebumian
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teknikSipilDropdown">
                            <li><a class="dropdown-item" href="../teknik_sipil_kebumian/sarjana_terapan_teknik_bangunan_rawa.php">D4 Sarjana Terapan Teknik Bangunan Rawa</a></li>
                            <li><a class="dropdown-item" href="../teknik_sipil_kebumian/sarjana_terapan_teknologi_rekayasa_geomatika.php">D4 Sarjana Terapan Teknologi Rekayasa Geomatika</a></li>
                            <li><a class="dropdown-item" href="../teknik_sipil_kebumian/sarjana_terapan_teknologi_rekayasa_konstruksi_jalan.php">D4 Sarjana Terapan Teknologi Rekayasa Konstruksi Jalan</a></li>
                            <li><a class="dropdown-item" href="../teknik_sipil_kebumian/diploma_teknik_sipil.php">D3 Teknik Sipil</a></li>
                            <li><a class="dropdown-item" href="../teknik_sipil_kebumian/diploma_teknik_pertambangan.php">D3 Teknik Pertambangan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="teknikMesinDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teknik Mesin
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teknikMesinDropdown">
                            <li><a class="dropdown-item" href="../teknik_mesin/sarjana_terapan_teknologi_rekayasa_otomotif.php">D4 Sarjana Terapan Teknologi Rekayasa Otomotif</a></li>
                            <li><a class="dropdown-item" href="../teknik_mesin/diploma_alat_berat.php">D3 Alat Berat</a></li>
                            <li><a class="dropdown-item" href="../teknik_mesin/diploma_teknik_mesin.php">D3 Teknik Mesin</a></li>
                            <li><a class="dropdown-item" href="../teknik_mesin/ddua_fast_track_tata_operasi_dan_pemeliharaan_prediktif.php">D2 Fast Track Tata Operasi dan Pemeliharaan Prediktif Alat Berat</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="administrasiBisnisDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Administrasi Bisnis
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="administrasiBisnisDropdown">
                            <li><a class="dropdown-item" href="../administrasi_bisnis/sarjana_terapan_bisnis_digital.php">D4 Sarjana Terapan Bisnis Digital</a></li>
                            <li><a class="dropdown-item" href="../administrasi_bisnis/diploma_administrasi_bisnis.php">D3 Administrasi Bisnis</a></li>
                            <li><a class="dropdown-item" href="../administrasi_bisnis/diploma_manajemen_informatika.php">D3 Manajemen Informatika</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="teknikElektroDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teknik Elektro
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teknikElektroDropdown">
                            <li><a class="dropdown-item" href="../teknik_elektro/sarjana_terapan_teknologi_rekayasa_pembangkit_energi.php">D4 Sarjana Terapan Teknologi Rekayasa Pembangkit Energi</a></li>
                            <li><a class="dropdown-item" href="../teknik_elektro/sarjana_terapan_sistem_informasi_kota_cerdas.php">D4 Sarjana Terapan Sistem Informasi Kota Cerdas</a></li>
                            <li><a class="dropdown-item" href="../teknik_elektro/sarjana_terapan_teknologi_rekayasa_otomasi.php">D4 Sarjana Terapan Teknologi Rekayasa Otomasi</a></li>
                            <li><a class="dropdown-item" href="../teknik_elektro/diploma_teknik_listrik.php">D3 Teknik Listrik</a></li>
                            <li><a class="dropdown-item" href="../teknik_elektro/diploma_teknik_informatika.php">D3 Teknik Informatika</a></li>
                            <li><a class="dropdown-item" href="../teknik_elektro/diploma_elektronika.php">D3 Elektronika</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="akuntansiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Akuntansi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="akuntansiDropdown">
                            <li><a class="dropdown-item" href="../akuntansi/sarjana_terapan_akuntansi_lembaga_keuangan_syariah.php">D4 Sarjana Terapan Akuntansi Lembaga Keuangan Syariah</a></li>
                            <li><a class="dropdown-item" href="../akuntansi/diploma_akuntansi.php">D3 Akuntansi</a></li>
                            <li><a class="dropdown-item" href="../akuntansi/diploma_komputerisasi_akuntansi.php">D3 Komputerisasi Akuntansi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link register" href="../auth/register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link login" href="../auth/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col text-center">
                <h2>Portofolio Mahasiswa Jurusan Administrasi Bisnis</h2>
                <p class="lead">Kumpulan karya terbaik dari mahasiswa dan alumni D3 Administrasi Bisnis</p>
            </div>
        </div>

        <!-- Carousel -->
        <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="../assets/images/poliban_1.jpeg" class="d-block mx-auto w-50" alt="..." style="max-height: 200px;">
                </div>
                <div class="carousel-item">
                    <img src="../assets/images/poliban_4.png" class="d-block mx-auto w-50" alt="..." style="max-height: 200px;">
                </div>
                <div class="carousel-item">
                    <img src="../assets/images/poliban_3.jpg" class="d-block mx-auto w-50" alt="..." style="max-height: 200px;">
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
        <form method="GET" action="diploma_administari_bisnis.php" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan keahlian atau judul sertifikasi, lomba, pelatihan, proyek" value="<?= htmlspecialchars($search) ?>">
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
                            <?php
                            $profileImage = !empty($portfolio['profile_image']) ? '../assets/mahasiswa/profile/' . htmlspecialchars($portfolio['profile_image']) : '../assets/images/profile_default.png';
                            ?>
                            <img src="<?= $profileImage ?>" class="card-img-left" alt="Profile <?= htmlspecialchars($portfolio['nama_mahasiswa']) ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
                            <div class="details">
                                <h5 class="card-title"><?= htmlspecialchars($portfolio['nama_mahasiswa']) ?></h5>
                                <p class="card-text">
                                    NIM: <?= htmlspecialchars($portfolio['nim']) ?><br>
                                    Jurusan: <?= htmlspecialchars($portfolio['jurusan']) ?><br>
                                    Prodi: <?= htmlspecialchars($portfolio['prodi']) ?><br>
                                    Keahlian: <?= htmlspecialchars($portfolio['keahlian']) ?: 'Belum ada keahlian' ?><br>
                                </p>
                                <div class="row">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <button type="button" class="btn btn-success more" data-bs-toggle="modal" data-bs-target="#modal<?= $portfolio['mahasiswa_id'] ?>">Selengkapnya</button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-primary portfolio" data-id="<?= htmlspecialchars($portfolio['mahasiswa_id']) ?>" data-bs-toggle="modal" data-bs-target="#portfolioModal">Portofolio</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="modal<?= $portfolio['mahasiswa_id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $portfolio['mahasiswa_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel<?= $portfolio['mahasiswa_id'] ?>">Detail Mahasiswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3 text-center">
                                        <div class="col-md-4 text-center">
                                            <?php
                                            $profileImage = !empty($portfolio['profile_image']) ? '../assets/mahasiswa/profile/' . htmlspecialchars($portfolio['profile_image']) : '../assets/images/profile_default.png';
                                            ?>
                                            <img src="<?= $profileImage ?>" class="img-fluid mb-3" alt="Profile <?= htmlspecialchars($portfolio['nama_mahasiswa']) ?>">
                                            <h5><?= htmlspecialchars($portfolio['nama_mahasiswa']) ?></h5>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 section-title">Informasi Dasar</div>
                                        <div class="col-12">
                                            <p class="card-text">
                                                <strong>NIM:</strong> <?= htmlspecialchars($portfolio['nim']) ?><br>
                                                <strong>Jurusan:</strong> <?= htmlspecialchars($portfolio['jurusan']) ?><br>
                                                <strong>Prodi:</strong> <?= htmlspecialchars($portfolio['prodi']) ?><br>
                                                <strong>Jenis Kelamin:</strong> <?= htmlspecialchars($portfolio['jk']) ?><br>
                                                <strong>Tahun Masuk:</strong> <?= htmlspecialchars($portfolio['tahun_masuk']) ?><br>
                                                <strong>Status:</strong> <?= htmlspecialchars($portfolio['status']) ?><br>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 section-title">Informasi Kontak</div>
                                        <div class="col-12">
                                            <p class="card-text">
                                                <strong>Alamat:</strong> <?= htmlspecialchars($portfolio['alamat']) ?><br>
                                                <strong>Email:</strong> <?= htmlspecialchars($portfolio['email']) ?><br>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 section-title">Keahlian</div>
                                        <div class="col-12 skills">
                                            <?php 
                                            $skills = explode(',', $portfolio['keahlian']);
                                            if (!empty($skills) && trim($portfolio['keahlian']) != ''): 
                                                foreach ($skills as $skill): ?>
                                                    <div class="skill"><?= htmlspecialchars($skill) ?></div>
                                                <?php endforeach; 
                                            else: ?>
                                                <p>Belum ada keahlian yang dimasukkan</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="footer mt-5">
        <div class="container">
        <div class="row">
            <div class="col text-center">
                <h5>Informasi Poliban</h5>
                    <p><i class="fas fa-map-marker-alt" style="color: red;"></i> Jl. Brigjen H. Hasan Basri, Kayu Tangi, Banjarmasin 70123</p>
                    <p><i class="fas fa-phone" style="color: green;"></i> Phone / Fax: (0511) 330 5052</p>
                    <p><i class="fas fa-envelope" style="color: blue;"></i> Email: info@poliban.ac.id</p>
                    <div class="social-icons">
                        <a href="https://web.facebook.com/poliban.ac.id?_rdc=1&_rdr" class="fab fa-facebook-f"></a>
                        <a href="https://x.com/humaspoliban" class="fab fa-twitter"></a>
                        <a href="https://www.instagram.com/poliban_official/" class="fab fa-instagram"></a>
                        <a href="https://www.youtube.com/channel/UC5CfzvUTqEUPXhwwSLvP53Q" class="fab fa-youtube"></a>
                    </div>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.374115719019!2d114.59080321476316!3d-3.3186032976042596!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de423bfc1e547b5%3A0x3030bfbca7b1748e!2sPoliteknik%20Negeri%20Banjarmasin!5e0!3m2!1sen!2sid!4v1623074784584!5m2!1sen!2sid" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.portfolio').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');

                fetch(`../fetch_portfolio_details.php?id=${id}`)
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
                                const buktiPath = bukti.map(b => `<a href="../assets/mahasiswa/sertifikasi/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
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
                                const buktiPath = bukti.map(b => `<a href="../assets/mahasiswa/lomba/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
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
                                const buktiPath = bukti.map(b => `<a href="../assets/mahasiswa/pelatihan/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
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
                                const buktiPath = bukti.map(b => `<a href="../assets/mahasiswa/proyek/${b}" target="_blank">Lihat Bukti</a>`).join(', ');
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


