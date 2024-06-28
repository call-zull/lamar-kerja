<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cdc') {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDC Profile</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">SI Portofolio</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" id="dark-mode-toggle" role="button">
                    <i class="fas fa-moon"></i>
                    Dark Mode
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="light-mode-toggle" role="button">
                    <i class="fas fa-sun"></i>
                    Light Mode
                </a>
            </li>
        </ul>
    </nav>

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
                        <a href="profile_cdc.php" class="nav-link active">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Profile</p>
                        </a>
                    </li>
                    <!-- Portofolio Mahasiswa -->
                    <li class="nav-item">
                        <a href="portofolio_mahasiswa_cdc.php" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Portofolio Mahasiswa</p>
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
                        <h1 class="m-0">Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-12 connectedSortable">
                        <!-- Custom tabs (Charts with tabs)-->
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
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </div>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="../path/to/cdc/image.jpg" class="img-fluid" alt="CDC Image">
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>NIP</th>
                                                    <td>123456789</td> <!-- Contoh NIP CDC -->
                                                </tr>
                                                <tr>
                                                    <th>Nama</th>
                                                    <td>Jane Doe</td> <!-- Contoh Nama CDC -->
                                                </tr>
                                                <tr>
                                                    <th>Alamat</th>
                                                    <td>Jalan Contoh No. 1</td> <!-- Contoh Alamat CDC -->
                                                </tr>
                                                <tr>
                                                    <th>Email</th>
                                                    <td>jane.doe@example.com</td> <!-- Contoh Email CDC -->
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </section>
                    <!-- /.Left col -->
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
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
    // Edit Profile Button
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const saveProfileBtn = document.getElementById('save-profile-btn');

    editProfileBtn.addEventListener('click', function () {
        editProfileBtn.classList.add('d-none');
        saveProfileBtn.classList.remove('d-none');
        // Enable input fields for editing
        enableInputFields();
    });

    saveProfileBtn.addEventListener('click', function () {
        editProfileBtn.classList.remove('d-none');
        saveProfileBtn.classList.add('d-none');
        // Save changes to profile
        saveProfileChanges();
        // Disable input fields
        disableInputFields();
    });

    function enableInputFields() {
        const inputFields = document.querySelectorAll('.profile-input');
        inputFields.forEach(function (field) {
            field.removeAttribute('disabled');
        });
    }

    function disableInputFields() {
        const inputFields = document.querySelectorAll('.profile-input');
        inputFields.forEach(function (field) {
            field.setAttribute('disabled', 'disabled');
        });
    }

    function saveProfileChanges() {
        // Implement save changes logic here
    }
</script>

</body>
</html>
