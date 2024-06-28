<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin')) {
    header('Location: ../auth/login.php');
    exit;
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Mahasiswa</title>
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
    <?php include 'navbar_admin.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Portofolio Mahasiswa</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Portofolio Mahasiswa</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

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
                                    <i class="fas fa-graduation-cap"></i>
                                    Data Portofolio Mahasiswa
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cetakModal">
                                        <i class="fas fa-print"></i> Cetak
                                    </button>
                                </div>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Cari mahasiswa...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <table id="mahasiswaTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Prodi</th>
                                                <th>Jurusan</th>
                                                <th>Tahun Masuk</th>
                                                <th>Status</th>
                                                <th>Sertifikat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data Mahasiswa -->
                                            <tr>
                                                <td>1</td>
                                                <td>1234567890</td>
                                                <td>John Doe</td>
                                                <td>Teknik Informatika</td>
                                                <td>Teknik</td>
                                                <td>2018</td>
                                                <td>Aktif</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sertifikatModal">
                                                        Lihat
                                                    </button>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                                    <button type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                                                </td>
                                            </tr>
                                            <!-- /Data Mahasiswa -->
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.card
                         <!-- Modal Lihat Sertifikat -->
<div class="modal fade" id="sertifikatModal" tabindex="-1" role="dialog" aria-labelledby="sertifikatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sertifikatModalLabel">Sertifikat Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sertifikat_lomba">Sertifikat Lomba</label>
                            <img src="../assets/images/sertifikat_lomba.jpg" class="img-fluid" alt="Sertifikat Lomba">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sertifikat_sertifikasi">Sertifikat Sertifikasi</label>
                            <img src="../assets/images/sertifikat_sertifikasi.jpg" class="img-fluid" alt="Sertifikat Sertifikasi">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sertifikat_pelatihan">Sertifikat Pelatihan</label>
                            <img src="../assets/images/sertifikat_pelatihan.jpg" class="img-fluid" alt="Sertifikat Pelatihan">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Lihat Sertifikat -->
<div class="modal fade" id="sertifikatModal" tabindex="-1" role="dialog" aria-labelledby="sertifikatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sertifikatModalLabel">Sertifikat Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sertifikat_lomba">Sertifikat Lomba</label>
                            <img src="../assets/images/sertifikat_lomba.jpg" class="img-fluid" alt="Sertifikat Lomba">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sertifikat_sertifikasi">Sertifikat Sertifikasi</label>
                            <img src="../assets/images/sertifikat_sertifikasi.jpg" class="img-fluid" alt="Sertifikat Sertifikasi">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sertifikat_pelatihan">Sertifikat Pelatihan</label>
                            <img src="../assets/images/sertifikat_pelatihan.jpg" class="img-fluid" alt="Sertifikat Pelatihan">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Cetak Portofolio -->
<div class="modal fade" id="cetakModal" tabindex="-1" role="dialog" aria-labelledby="cetakModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cetakModalLabel">Cetak Portofolio Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin mencetak portofolio mahasiswa?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Cetak</button>
            </div>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="script_admin.js"></script>
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>

</body>
</html>



