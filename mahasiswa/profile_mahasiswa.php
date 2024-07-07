<?php
session_start();

// Pastikan pengguna sudah login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Fetch mahasiswa profile information
$user_id = $_SESSION['user_id'];
$sql = "SELECT m.id, m.nim, m.nama_mahasiswa, m.prodi_id, m.jurusan_id, p.nama_prodi, j.nama_jurusan, m.tahun_masuk, m.status, m.jk, m.alamat, m.email, m.no_telp, m.profile_image
        FROM mahasiswas m
        LEFT JOIN prodis p ON m.prodi_id = p.id
        LEFT JOIN jurusans j ON m.jurusan_id = j.id
        WHERE m.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// Check if mahasiswa data is found
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    die("Data mahasiswa tidak ditemukan.");
}

// Fetch all jurusan and prodi options for dropdowns
$sql_jurusans = "SELECT * FROM jurusans";
$stmt_jurusans = $pdo->prepare($sql_jurusans);
$stmt_jurusans->execute();
$jurusans = $stmt_jurusans->fetchAll(PDO::FETCH_ASSOC);

$sql_prodis = "SELECT * FROM prodis";
$stmt_prodis = $pdo->prepare($sql_prodis);
$stmt_prodis->execute();
$prodis = $stmt_prodis->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Mahasiswa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <style>
       .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .btn-bottom-left {
            position: fixed;
            bottom: 20px;
            left: 20px;
        }
        .profile-img {
            max-width: 200px; /* Ubah ukuran gambar di sini */
            max-height: 200px; /* Ubah ukuran gambar di sini */
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Nav bar -->
    <?php include 'navbar_mhs.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Profile Mahasiswa</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Profile Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-lg-12 connectedSortable">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user"></i>
                                    Profile
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" id="edit-profile-btn">
                                        <i class="fas fa-edit"></i> Edit Profile
                                    </button>
                                    <button type="button" class="btn btn-success d-none" id="save-profile-btn">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="profile-form" action="update_profile.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <!-- Display profile image and upload form -->
                                            <?php if (!empty($profile['profile_image'])) : ?>
                                                <img src="../assets/mahasiswa/profile/<?= htmlspecialchars($profile['profile_image']) ?>" class="img-fluid mb-3" alt="Mahasiswa Image">
                                            <?php else : ?>
                                                <img src="../assets/images/profile_default.png" class="img-fluid mb-3" alt="Default Image">
                                            <?php endif; ?>
                                            <div class="form-group">
                                                <label for="fileToUpload" class="form-label">Profile Image</label>
                                                <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <!-- Display profile information -->
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($profile['id']) ?>">
                                            <div class="form-group">
                                                <label for="nim">NIM</label>
                                                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($profile['nim']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="nama">Nama</label>
                                                <input type="text" class="form-control profile-input" id="nama" name="nama_mahasiswa" value="<?= htmlspecialchars($profile['nama_mahasiswa']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="jurusan">Jurusan</label>
                                                <select class="form-control profile-input" id="jurusan" name="jurusan_id" readonly>
                                                    <?php foreach ($jurusans as $jurusan) : ?>
                                                        <option value="<?= htmlspecialchars($jurusan['id']) ?>" <?= ($jurusan['id'] == $profile['jurusan_id']) ? 'selected' : '' ?>><?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="prodi">Prodi</label>
                                                <select class="form-control profile-input" id="prodi" name="prodi_id" readonly>
                                                    <?php foreach ($prodis as $prodi) : ?>
                                                        <option value="<?= htmlspecialchars($prodi['id']) ?>" <?= ($prodi['id'] == $profile['prodi_id']) ? 'selected' : '' ?>><?= htmlspecialchars($prodi['nama_prodi']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="tahun_masuk">Tahun Masuk</label>
                                                <input type="number" class="form-control profile-input" id="tahun_masuk" name="tahun_masuk" value="<?= htmlspecialchars($profile['tahun_masuk']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control profile-input" id="status" name="status" readonly>
                                                    <option value="mahasiswa aktif" <?= ($profile['status'] == 'mahasiswa aktif') ? 'selected' : '' ?>>Mahasiswa Aktif</option>
                                                    <option value="alumni" <?= ($profile['status'] == 'alumni') ? 'selected' : '' ?>>Alumni</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="jk">Jenis Kelamin</label>
                                                <select class="form-control profile-input" id="jk" name="jk" readonly>
                                                    <option value="laki-laki" <?= ($profile['jk'] == 'laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                                    <option value="perempuan" <?= ($profile['jk'] == 'perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="alamat">Alamat</label>
                                                <input type="text" class="form-control profile-input" id="alamat" name="alamat" value="<?= htmlspecialchars($profile['alamat']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control profile-input" id="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">No. HP</label>
                                                <input type="tel" class="form-control profile-input" id="no_hp" name="no_telp" value="<?= htmlspecialchars($profile['no_telp']) ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script>
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const saveProfileBtn = document.getElementById('save-profile-btn');
    const fileToUpload = document.getElementById('fileToUpload');
    const formElements = document.querySelectorAll('.profile-input');

    editProfileBtn.addEventListener('click', function() {
        editProfileBtn.classList.add('d-none');
        saveProfileBtn.classList.remove('d-none');
        enableEditing();
    });

    saveProfileBtn.addEventListener('click', function() {
        document.getElementById('profile-form').submit();
    });

    fileToUpload.addEventListener('change', function() {
        if (fileToUpload.files.length > 0) {
            saveProfileBtn.classList.remove('d-none');
        }
    });

    function enableEditing() {
        formElements.forEach(element => {
            element.removeAttribute('readonly');
        });
    }
</script>
</body>

</html>
