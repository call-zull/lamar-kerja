<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perusahaan</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 20px !important;
        }
        .content-header {
            padding: 20px 0 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
   <!-- Nav bar -->
   <?php include 'navbar_mhs.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cari Lowongan yang Sesuai</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Perusahaan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan A</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Natus minima temporibus nam fugiat corrupti labore eaque vero maiores alias, ipsa ducimus eveniet itaque ipsam perferendis nihil quia! Accusamus, eveniet autem.</p>
                                <a href="#" class="btn btn-primary">Lamar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan B</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus facere expedita corrupti, soluta vitae corporis eligendi sunt eveniet consectetur nam error praesentium cupiditate velit nobis magni voluptatem necessitatibus dicta? Rem.</p>
                                <a href="#" class="btn btn-primary">Lamar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan C</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta, ullam eum. Quis esse sit exercitationem quia asperiores veniam voluptatibus, nesciunt quisquam quo accusamus dolor aperiam nam. Placeat nesciunt excepturi doloremque!</p>
                                <a href="#" class="btn btn-primary">Lamar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan D</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Vel deserunt reprehenderit autem, eveniet eius amet magnam expedita esse, ad ullam, magni id porro nobis repellendus placeat dicta! Praesentium, aperiam hic?</p>
                                <a href="#" class="btn btn-primary">Lamar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="../app/dist/js/demo.js"></script>
<script>
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    darkModeToggle.addEventListener('click', function () {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    });

    const lightModeToggle = document.getElementById('light-mode-toggle');
    lightModeToggle.addEventListener('click', function () {
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode');
    });
</script>

</body>
</html>
