<?php
session_start();
// Pastikan pengguna sudah login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Contoh data profil mahasiswa dengan data kosong
$profile = [
    'nim' => '',
    'nama' => '',
    'prodi' => '',
    'jurusan' => '',
    'tahun_masuk' => '',
    'status' => '',
    'jenis_kelamin' => '',
    'alamat' => '',
    'email' => '',
    'no_hp' => ''
];

// Daftar Prodi dan Jurusan yang tersedia
$prodi_list = ['Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro'];
$jurusan_list = ['Teknik', 'Manajemen', 'Bisnis', 'Sains'];
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
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
   <!-- Nav bar -->
   <?php include 'navbar_mhs.php'; ?>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <img src="../app/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">SI Portofolio</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <!-- Profile -->
                    <li class="nav-item">
                        <a href="profile_mahasiswa.php" class="nav-link active">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Profile</p>
                        </a>
                    </li>
                    <!-- Data Portofolio -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Data Portofolio <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- Kompetensi -->
                            <li class="nav-item">
                                <a href="data_kompetensi.php" class="nav-link">
                                    <i class="fas fa-tasks nav-icon"></i>
                                    <p>Kompetensi</p>
                                </a>
                            </li>
                            <!-- Lomba -->
                            <li class="nav-item">
                                <a href="data_lomba.php" class="nav-link">
                                    <i class="fas fa-trophy nav-icon"></i>
                                    <p>Lomba</p>
                                </a>
                            </li>
                            <!-- Pelatihan -->
                            <li class="nav-item">
                                <a href="data_pelatihan.php" class="nav-link">
                                    <i class="fas fa-chalkboard-teacher nav-icon"></i>
                                    <p>Pelatihan</p>
                                </a>
                            </li>
                            <!-- Proyek -->
                            <li class="nav-item">
                                <a href="data_proyek.php" class="nav-link">
                                    <i class="far fa-folder-open nav-icon"></i>
                                    <p>Proyek</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Cari Kerja -->
                    <li class="nav-item">
                        <a href="cari_kerja.php" class="nav-link">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>Cari Kerja</p>
                        </a>
                    </li>
                    <!-- Logout -->
                    <li class="nav-item">
                        <a href="../auth/logout.php" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Profile Mahasiswa</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">Profile Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

       <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Foto Profil</h3>
                            </div>
                            <div class="card-body text-center">
                                <img src="../app/dist/img/user2-160x160.jpg" class="img-fluid" alt="Foto Profil">
                            </div>

                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <form action="simpan_profile.php" method="POST">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="nim">NIM</label>
                                        <input type="text" class="form-control" id="nim" name="nim" value="<?php echo $profile['nim']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $profile['nama']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="prodi">Prodi</label>
                                        <select class="form-control" id="prodi" name="prodi">
                                            <?php foreach ($prodi_list as $prodi) { ?>
                                                <option value="<?php echo $prodi; ?>" <?php echo $profile['prodi'] == $prodi ? 'selected' : ''; ?>><?php echo $prodi; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jurusan">Jurusan</label>
                                        <select class="form-control" id="jurusan" name="jurusan">
                                            <?php foreach ($jurusan_list as $jurusan) { ?>
                                                <option value="<?php echo $jurusan; ?>" <?php echo $profile['jurusan'] == $jurusan ? 'selected' : ''; ?>><?php echo $jurusan; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="tahun_masuk">Tahun Masuk</label>
                                        <input type="text" class="form-control" id="tahun_masuk" name="tahun_masuk" value="<?php echo $profile['tahun_masuk']; ?>">
                                        </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <input type="text" class="form-control" id="status" name="status" value="<?php echo $profile['status']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                            <option value="Laki-Laki" <?php echo $profile['jenis_kelamin'] == 'Laki-Laki' ? 'selected' : ''; ?>>Laki-Laki</option>
                                            <option value="Perempuan" <?php echo $profile['jenis_kelamin'] == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat">Alamat</label>
                                        <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $profile['alamat']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $profile['email']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">No HP</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $profile['no_hp']; ?>">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="enableEditing()">Edit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
</div>
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="../app/dist/js/demo.js"></script>
<script>
    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    darkModeToggle.addEventListener('click', function () {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    });

    // Light Mode Toggle
    const lightModeToggle = document.getElementById('light-mode-toggle');
    lightModeToggle.addEventListener('click', function () {
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode');
    });

    function enableEditing() {
        const formElements = document.querySelectorAll('input, select');
        formElements.forEach(element => {
            element.removeAttribute('readonly');
        });
    }
</script>

</body>
</html>

