<?php
session_start();
include '../../includes/db.php';
// Pastikan pengguna sudah login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Contoh data lomba mahasiswa
$lomba = [
    ['no' => 1, 'nama_lomba' => 'Lomba A', 'kategori' => 'Kategori 1', 'prestasi' => 'Juara 1', 'tingkatan' => 'Nasional', 'tanggal_tahun' => '2023-05-20', 'tempat' => 'Jakarta', 'penyelenggara' => 'Penyelenggara A'],
    ['no' => 2, 'nama_lomba' => 'Lomba B', 'kategori' => 'Kategori 2', 'prestasi' => 'Juara 2', 'tingkatan' => 'Internasional', 'tanggal_tahun' => '2022-08-15', 'tempat' => 'Bandung', 'penyelenggara' => 'Penyelenggara B']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Lomba Mahasiswa</title>
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

        .context-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ddd;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .context-menu a {
            color: #333;
            display: block;
            padding: 8px 10px;
            text-decoration: none;
        }

        .context-menu a:hover {
            background-color: #f2f2f2;
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
                        <h1 class="m-0">Tampil Data Lomba</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Tampil Data Lomba</li>
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
                                    Data Lomba
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCompetencyModal">
                                        <i class="fas fa-user-plus"></i> Tambah 
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="kompetensiTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Lomba</th>
                                                <th>Kategori</th>
                                                <th>Prestasi</th>
                                                <th>Tingkatan</th>
                                                <th>Tanggal/Tahun</th>
                                                <th>Tempat Pelaksanaan</th>
                                                <th>Penyelenggara</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lomba as $index => $item) { ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo $item['nama_lomba']; ?></td>
                                                    <td><?php echo $item['kategori']; ?></td>
                                                    <td><?php echo $item['prestasi']; ?></td>
                                                    <td><?php echo $item['tingkatan']; ?></td>
                                                    <td><?php echo $item['tanggal_tahun']; ?></td>
                                                    <td><?php echo $item['tempat']; ?></td>
                                                    <td><?php echo $item['penyelenggara']; ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-btn" data-index="<?php echo $index; ?>" data-toggle="modal" data-target="#editLombaModal"><i class="fas fa-edit"></i> Edit</button>
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

<!-- Modal Tambah Lomba -->
<div class="modal fade" id="addLombaModal" tabindex="-1" role="dialog" aria-labelledby="addLombaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLombaModalLabel">Tambah Lomba</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addLombaForm">
                    <div class="form-group">
                        <label for="nama_lomba">Nama Lomba</label>
                        <input type="text" class="form-control" id="nama_lomba" name="nama_lomba">
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori">
                    </div>
                    <div class="form-group">
                        <label for="prestasi">Prestasi</label>
                        <input type="text" class="form-control" id="prestasi" name="prestasi">
                    </div>
                    <div class="form-group">
                        <label for="tingkatan">Tingkatan</label>
                        <input type="text" class="form-control" id="tingkatan" name="tingkatan">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_tahun">Tanggal/Tahun</label>
                        <input type="date" class="form-control" id="tanggal_tahun" name="tanggal_tahun">
                    </div>
                    <div class="form-group">
                        <label for="tempat">Tempat Pelaksanaan</label>
                        <input type="text" class="form-control" id="tempat" name="tempat">
                    </div>
                    <div class="form-group">
                        <label for="penyelenggara">Penyelenggara</label>
                        <input type="text" class="form-control" id="penyelenggara" name="penyelenggara">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="addLomba()">Tambah</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit Lomba -->
<div class="modal fade" id="editLombaModal" tabindex="-1" role="dialog" aria-labelledby="editLombaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLombaModalLabel">Edit Lomba</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLombaForm">
                    <input type="hidden" id="editRowIndex">
                    <div class="form-group">
                        <label for="edit_nama_lomba">Nama Lomba</label>
                        <input type="text" class="form-control" id="edit_nama_lomba" name="edit_nama_lomba">
                    </div>
                    <div class="form-group">
                        <label for="edit_kategori">Kategori</label>
                        <input type="text" class="form-control" id="edit_kategori" name="edit_kategori">
                    </div>
                    <div class="form-group">
                        <label for="edit_prestasi">Prestasi</label>
                        <input type="text" class="form-control" id="edit_prestasi" name="edit_prestasi">
                    </div>
                    <div class="form-group">
                        <label for="edit_tingkatan">Tingkatan</label>
                        <input type="text" class="form-control" id="edit_tingkatan" name="edit_tingkatan">
                    </div>
                    <div class="form-group">
                        <label for="edit_tanggal_tahun">Tanggal/Tahun</label>
                        <input type="date" class="form-control" id="edit_tanggal_tahun" name="edit_tanggal_tahun">
                    </div>
                    <div class="form-group">
                        <label for="edit_tempat">Tempat Pelaksanaan</label>
                        <input type="text" class="form-control" id="edit_tempat" name="edit_tempat">
                    </div>
                    <div class="form-group">
                        <label for="edit_penyelenggara">Penyelenggara</label>
                        <input type="text" class="form-control" id="edit_penyelenggara" name="edit_penyelenggara">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="editLomba()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../app/dist/js/adminlte.min.js"></script>
<script>
$(document).ready(function() {
    // Tambah Lomba
    function addLomba() {
        // Ambil nilai dari form tambah lomba
        var nama_lomba = $('#nama_lomba').val();
        var kategori = $('#kategori').val();
        var prestasi = $('#prestasi').val();
        var tingkatan = $('#tingkatan').val();
        var tanggal_tahun = $('#tanggal_tahun').val();
        var tempat = $('#tempat').val();
        var penyelenggara = $('#penyelenggara').val();

        // Lakukan validasi data (sesuai kebutuhan)

        // Kirim data ke backend via AJAX (misalnya process_add_lomba.php)
        $.ajax({
            url: 'process_add_lomba.php',
            type: 'POST',
            data: {
                nama_lomba: nama_lomba,
                kategori: kategori,
                prestasi: prestasi,
                tingkatan: tingkatan,
                tanggal_tahun: tanggal_tahun,
                tempat: tempat,
                penyelenggara: penyelenggara
            },
            success: function(response) {
                // Tampilkan pesan sukses atau error jika ada
                console.log(response); // Untuk debugging
                // Reset form atau tampilkan pesan sukses
                $('#addLombaModal').modal('hide');
                // Reload data lomba atau tampilkan pesan sukses
            },
            error: function(xhr, status, error) {
                // Tampilkan pesan error jika terjadi masalah
                console.error(xhr.responseText); // Untuk debugging
                alert('Terjadi kesalahan saat menambah lomba. Silakan coba lagi.');
            }
        });
    }

    // Edit Lomba
    $('#editLombaModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Tombol yang memunculkan modal
        var index = button.data('index'); // Mendapatkan data-index dari tombol

        // Mengisi nilai pada form edit modal
        $('#editRowIndex').val(index);
        $('#edit_nama_lomba').val(lomba[index]['nama_lomba']);
        $('#edit_kategori').val(lomba[index]['kategori']);
        $('#edit_prestasi').val(lomba[index]['prestasi']);
        $('#edit_tingkatan').val(lomba[index]['tingkatan']);
        $('#edit_tanggal_tahun').val(lomba[index]['tanggal_tahun']);
        $('#edit_tempat').val(lomba[index]['tempat']);
        $('#edit_penyelenggara').val(lomba[index]['penyelenggara']);
    });

    function editLomba() {
        var index = $('#editRowIndex').val();
        // Ambil nilai dari form edit lomba
        var nama_lomba = $('#edit_nama_lomba').val();
        var kategori = $('#edit_kategori').val();
        var prestasi = $('#edit_prestasi').val();
        var tingkatan = $('#edit_tingkatan').val();
        var tanggal_tahun = $('#edit_tanggal_tahun').val();
        var tempat = $('#edit_tempat').val();
        var penyelenggara = $('#edit_penyelenggara').val();

        // Kirim data ke backend via AJAX (misalnya process_edit_lomba.php)
        $.ajax({
            url: 'process_edit_lomba.php',
            type: 'POST',
            data: {
                index: index,
                nama_lomba: nama_lomba,
                kategori: kategori,
                prestasi: prestasi,
                tingkatan: tingkatan,
                tanggal_tahun: tanggal_tahun,
                tempat: tempat,
                penyelenggara: penyelenggara
            },
            success: function(response) {
                // Tampilkan pesan sukses atau error jika ada
                console.log(response); // Untuk debugging
                // Reset form atau tampilkan pesan sukses
                $('#editLombaModal').modal('hide');
                // Reload data lomba atau tampilkan pesan sukses
            },
            error: function(xhr, status, error) {
                // Tampilkan pesan error jika terjadi masalah
                console.error(xhr.responseText); // Untuk debugging
                alert('Terjadi kesalahan saat menyimpan perubahan. Silakan coba lagi.');
            }
        });
    }

    // Hapus Lomba
    $(document).on('click', '.delete-btn', function() {
        var index = $(this).data('index');

        // Kirim data ke backend via AJAX (misalnya process_delete_lomba.php)
        $.ajax({
            url: 'process_delete_lomba.php',
            type: 'POST',
            data: {
                index: index
            },
            success: function(response) {
                // Tampilkan pesan sukses atau error jika ada
                console.log(response); // Untuk debugging
                // Reload data lomba atau tampilkan pesan sukses
            },
            error: function(xhr, status, error) {
                // Tampilkan pesan error jika terjadi masalah
                console.error(xhr.responseText); // Untuk debugging
                alert('Terjadi kesalahan saat menghapus data lomba. Silakan coba lagi.');
            }
        });
    });
});
</script>
