<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliban Mahasiswa Portofolio</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .navbar-nav .nav-link {
        color: white;
        }
        .navbar-nav .nav-link.login {
            color: white;
            font-weight: bold;
        }
        .navbar-nav .nav-link.web-poliban {
            color: #007bff; /* warna biru seperti warna tautan default */
        }
        .navbar-nav .nav-link.web-poliban:hover {
            color: #0056b3; /* warna biru lebih tua saat hover */
        }
        .card {
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-img-left {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
            align-self: center;
        }
        .details {
            flex: 1;
            overflow: hidden;
            text-align: center;
        }
        .more {
            position: absolute;
            bottom: 15px;
            right: 15px;
        }
        @media (min-width: 768px) {
            .card-body {
                display: flex;
                flex-direction: row;
                align-items: center;
            }
            .card-img-left {
                margin-right: 15px;
                margin-bottom: 0;
            }
            .details {
                text-align: left;
            }
        }
         /* Latar Belakang */
         .login-page {
            background-image: url('../assets/images/poliban_background.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        /* Logo */
        .logo {
            width: 80px; /* Ubah ukuran logo sesuai kebutuhan */
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
                        <li class="nav-item">
                            <a class="nav-link web-poliban" href="https://www.poliban.ac.id/">Web Poliban</a>
                        </li>
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
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="query" placeholder="Cari berdasarkan prodi atau keahlian" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Cari</button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Contoh Portofolio (5 kolom x 4 baris) -->
        <?php
        $mahasiswa = [
            [
                'nama' => 'Nama Mahasiswa 1',
                'nim' => '1234567890',
                'jurusan' => 'Teknik Informatika',
                'prodi' => 'Manajemen Informatika',
                'keahlian' => 'Pemrograman Web',
                'gambar' => 'app/dist/img/user1-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/1'
            ],
            [
                'nama' => 'Nama Mahasiswa 2',
                'nim' => '2345678901',
                'jurusan' => 'Teknik Sipil',
                'prodi' => 'Teknik Konstruksi',
                'keahlian' => 'Desain Struktur',
                'gambar' => 'app/dist/img/user2-160x160.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/2'
            ],
            [
                'nama' => 'Nama Mahasiswa 3',
                'nim' => '3456789012',
                'jurusan' => 'Teknik Elektro',
                'prodi' => 'Sistem Tenaga',
                'keahlian' => 'Elektronika Industri',
                'gambar' => 'app/dist/img/user3-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/3'
            ],
            [
                'nama' => 'Nama Mahasiswa 4',
                'nim' => '4567890123',
                'jurusan' => 'Teknik Mesin',
                'prodi' => 'Manufaktur',
                'keahlian' => 'Desain Mesin',
                'gambar' => 'app/dist/img/user4-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/4'
            ],
            [
                'nama' => 'Nama Mahasiswa 5',
                'nim' => '5678901234',
                'jurusan' => 'Teknik Kimia',
                'prodi' => 'Teknologi Proses',
                'keahlian' => 'Rekayasa Kimia',
                'gambar' => 'app/dist/img/user5-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/5'
            ],
            [
                'nama' => 'Nama Mahasiswa 6',
                'nim' => '6789012345',
                'jurusan' => 'Teknik Informatika',
                'prodi' => 'Manajemen Informatika',
                'keahlian' => 'Data Science',
                'gambar' => 'app/dist/img/user6-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/6'
            ],
            [
                'nama' => 'Nama Mahasiswa 7',
                'nim' => '7890123456',
                'jurusan' => 'Teknik Sipil',
                'prodi' => 'Teknik Konstruksi',
                'keahlian' => 'Manajemen Proyek',
                'gambar' => 'app/dist/img/user7-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/7'
            ],
            [
                'nama' => 'Nama Mahasiswa 8',
                'nim' => '8901234567',
                'jurusan' => 'Teknik Elektro',
                'prodi' => 'Sistem Tenaga',
                'keahlian' => 'Robotika',
                'gambar' => 'app/dist/img/user8-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/8'
            ],
            [
                'nama' => 'Nama Mahasiswa 9',
                'nim' => '9012345678',
                'jurusan' => 'Teknik Mesin',
                'prodi' => 'Manufaktur',
                'keahlian' => 'Otomasi',
                'gambar' => 'app/dist/img/user1-128x128.jpg',
                'portofolio' => 'https://drive.google.com/drive/folders/9'
            ]
        ];

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $index = $i * 5 + $j;
                if (isset($mahasiswa[$index])) {
                    $mhs = $mahasiswa[$index];
                    echo '
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body d-flex">
                                <img src="'.$mhs['gambar'].'" class="card-img-left" alt="Portofolio '.$mhs['nama'].'">
                                <div class="details">
                                    <h5 class="card-title">'.$mhs['nama'].'</h5>
                                    <p class="card-text">
                                        NIM: '.$mhs['nim'].'<br>
                                        Jurusan: '.$mhs['jurusan'].'<br>
                                        Prodi: '.$mhs['prodi'].'<br>
                                        Keahlian: '.$mhs['keahlian'].'<br>
                                        <a href="'.$mhs['portofolio'].'" target="_blank">Link Portofolio</a>
                                    </p>
                                </div>
                                <button type="button" class="btn btn-link more" data-bs-toggle="modal" data-bs-target="#modal'.$index.'">Selengkapnya</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="modal'.$index.'" tabindex="-1" aria-labelledby="modalLabel'.$index.'" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel'.$index.'">Detail Mahasiswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="'.$mhs['gambar'].'" class="img-fluid rounded-circle" alt="Portofolio '.$mhs['nama'].'">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="details">
                                                <h5 class="card-title">'.$mhs['nama'].'</h5>
                                                <p class="card-text">
                                                    NIM: '.$mhs['nim'].'<br>
                                                    Jurusan: '.$mhs['jurusan'].'<br>
                                                    Prodi: '.$mhs['prodi'].'<br>
                                                    Keahlian: '.$mhs['keahlian'].'<br>
                                                    <a href="'.$mhs['portofolio'].'" target="_blank">Link Portofolio</a>
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
                    </div>';
                }
            }
        }
        ?>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

