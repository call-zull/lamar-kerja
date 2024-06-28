<?php
session_start();
// Pastikan pengguna sudah login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Contoh data pelatihan mahasiswa
$pelatihan = [
    ['no' => 1, 'nama_pelatihan' => 'Pelatihan A', 'durasi' => '3 Hari', 'tanggal_pelaksanaan' => '2023-05-20', 'tempat' => 'Jakarta', 'materi' => 'Materi A', 'penyelenggara' => 'Penyelenggara A'],
    ['no' => 2, 'nama_pelatihan' => 'Pelatihan B', 'durasi' => '5 Hari', 'tanggal_pelaksanaan' => '2022-08-15', 'tempat' => 'Bandung', 'materi' => 'Materi B', 'penyelenggara' => 'Penyelenggara B']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelatihan Mahasiswa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .btn-right {
            float: right;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <?php include 'navbar_mhs.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <!-- Success Message -->
                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Pelatihan Mahasiswa</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Data Pelatihan Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Data Card -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users"></i>
                                    Data Pelatihan
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPelatihanModal">
                                        <i class="fas fa-user-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="pelatihanTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pelatihan</th>
                                                <th>Durasi</th>
                                                <th>Tanggal Pelaksanaan</th>
                                                <th>Tempat</th>
                                                <th>Materi</th>
                                                <th>Penyelenggara</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pelatihan as $index => $item) { ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo $item['nama_pelatihan']; ?></td>
                                                    <td><?php echo $item['durasi']; ?></td>
                                                    <td><?php echo $item['tanggal_pelaksanaan']; ?></td>
                                                    <td><?php echo $item['tempat']; ?></td>
                                                    <td><?php echo $item['materi']; ?></td>
                                                    <td><?php echo $item['penyelenggara']; ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-btn" data-index="<?php echo $index; ?>" data-toggle="modal" data-target="#editPelatihanModal"><i class="fas fa-edit"></i> Edit</button>
                                                        <button class="btn btn-danger btn-sm delete-btn" data-index="<?php echo $index; ?>"><i class="fas fa-trash"></i> Hapus</button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /.card-body -->
                        </div><!-- /.card -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

<!-- Modal Tambah Pelatihan -->
<div class="modal fade" id="addPelatihanModal" tabindex="-1" role="dialog" aria-labelledby="addPelatihanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPelatihanModalLabel">Tambah Pelatihan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addPelatihanForm">
                    <div class="form-group">
                        <label for="nama_pelatihan">Nama Pelatihan</label>
                        <input type="text" class="form-control" id="nama_pelatihan" name="nama_pelatihan">
                    </div>
                    <div class="form-group">
                        <label for="durasi">Durasi</label>
                        <input type="text" class="form-control" id="durasi" name="durasi">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pelaksanaan">Tanggal Pelaksanaan</label>
                        <input type="date" class="form-control" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan">
                    </div>
                    <div class="form-group">
                        <label for="tempat">Tempat</label>
                        <input type="text" class="form-control" id="tempat" name="tempat">
                    </div>
                    <div class="form-group">
                        <label for="materi">Materi</label>
                        <input type="text" class="form-control" id="materi" name="materi">
                    </div>
                    <div class="form-group">
                        <label for="penyelenggara">Penyelenggara</label>
                        <input type="text" class="form-control" id="penyelenggara" name="penyelenggara">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="addPelatihan()">Tambah</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pelatihan -->
<div class="modal fade" id="editPelatihanModal" tabindex="-1" role="dialog" aria-labelledby="editPelatihanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPelatihanModalLabel">Edit Pelatihan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPelatihanForm">
                    <input type="hidden" id="editRowIndex">
                    <div class="form-group">
                        <label for="edit_nama_pelatihan">Nama Pelatihan</label>
                        <input type="text" class="form-control" id="edit_nama_pelatihan" name="edit_nama_pelatihan">
                    </div>
                    <div class="form-group">
                        <label for="edit_durasi">Durasi</label>
                        <input type="text" class="form-control" id="edit_durasi" name="edit_durasi">
                    </div>
                    <div class="form-group">
                        <label for="edit_tanggal_pelaksanaan">Tanggal Pelaksanaan</label>
                        <input type="date" class="form-control" id="edit_tanggal_pelaksanaan" name="edit_tanggal_pelaksanaan">
                    </div>
                    <div class="form-group">
                        <label for="edit_tempat">Tempat</label>
                        <input type="text" class="form-control" id="edit_tempat" name="edit_tempat">
                    </div>
                    <div class="form-group">
                        <label for="edit_materi">Materi</label>
                        <input type="text" class="form-control" id="edit_materi" name="edit_materi">
                    </div>
                    <div class="form-group">
                        <label for="edit_penyelenggara">Penyelenggara</label>
                        <input type="text" class="form-control" id="edit_penyelenggara" name="edit_penyelenggara">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="updatePelatihan()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Pelatihan -->
<div class="modal fade" id="deletePelatihanModal" tabindex="-1" role="dialog" aria-labelledby="deletePelatihanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePelatihanModalLabel">Hapus Pelatihan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pelatihan ini?</p>
                <input type="hidden" id="deletePelatihanIndex">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deletePelatihan()">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="../../app/plugins/jquery/jquery.min.js"></script>
<script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    function addPelatihan() {
        // Implementasi logika tambah pelatihan
    }

    function updatePelatihan() {
        // Implementasi logika update pelatihan
    }

    function deletePelatihan() {
        // Implementasi logika hapus pelatihan
    }
</script>
</body>
</html>
