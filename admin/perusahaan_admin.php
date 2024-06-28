<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Include Navbar and Sidebar for admin -->
    <?php include 'navbar_admin.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Perusahaan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Perusahaan</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan A</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Natus minima temporibus nam fugiat corrupti labore eaque vero maiores alias, ipsa ducimus eveniet itaque ipsam perferendis nihil quia! Accusamus, eveniet autem.</p>
                                <a href="#" class="btn btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan B</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus facere expedita corrupti, soluta vitae corporis eligendi sunt eveniet consectetur nam error praesentium cupiditate velit nobis magni voluptatem necessitatibus dicta? Rem.</p>
                                <a href="#" class="btn btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan C</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta, ullam eum. Quis esse sit exercitationem quia asperiores veniam voluptatibus, nesciunt quisquam quo accusamus dolor aperiam nam. Placeat nesciunt excepturi doloremque!</p>
                                <a href="#" class="btn btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perusahaan D</h5>
                                <p class="card-text">Kualifikasi:</p>
                                <p class="card-text">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Vel deserunt reprehenderit autem, eveniet eius amet magnam expedita esse, ad ullam, magni id porro nobis repellendus placeat dicta! Praesentium, aperiam hic?</p>
                                <a href="#" class="btn btn-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <!-- Include JavaScript files for admin -->
    <script src="script_admin.js"></script>
<script src="../includes/script_admin.js"></script>
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>

</div>
</body>
</html>
