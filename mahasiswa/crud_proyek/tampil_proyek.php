<?php
session_start();
include '../../includes/db.php';

// Pastikan pengguna masuk sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

// Pesan keberhasilan
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Ambil data proyek dari database
$mahasiswa_id = $_SESSION['mahasiswa_id'];
$sql = "SELECT id, nama_proyek, partner, peran, waktu_awal, waktu_selesai, tujuan_proyek, bukti 
        FROM proyek WHERE mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$mahasiswa_id]);
$proyek = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil Data Proyek</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link rel="stylesheet" href="../../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .table-responsive {
            overflow-y: auto;
            max-height: 400px; /* Adjust height as needed */
        }

        .table-responsive thead {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #343a40; /* Ensure the header has a background */
        }

        .table-responsive thead th {
        color: #ffffff;
        border-color: #454d55; 
        }

        .table-responsive tbody tr:hover {
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
                <!-- <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tampil Data Proyek</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Tampil Data Proyek</li>
                        </ol>
                    </div>
                </div> -->
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
                                    <i class="fas fa-award nav-icon"></i>
                                    Data Proyek
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProyekModal">
                                        <i class="fas fa-plus-circle"></i> Tambah 
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="proyekTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Proyek</th>
                                                <th>Partner</th>
                                                <th>Peran</th>
                                                <th>Waktu Awal</th>
                                                <th>Waktu Selesai</th>
                                                <th>Tujuan Proyek</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($proyek): ?>
                                                <?php foreach ($proyek as $index => $item): ?>
                                                    <tr>
                                                        <td><?= $index + 1; ?></td>
                                                        <td><?= htmlspecialchars($item['nama_proyek']); ?></td>
                                                        <td><?= htmlspecialchars($item['partner']); ?></td>
                                                        <td><?= htmlspecialchars($item['peran']); ?></td>
                                                        <td><?= htmlspecialchars($item['waktu_awal']); ?></td>
                                                        <td><?= htmlspecialchars($item['waktu_selesai']); ?></td>
                                                        <td><?= htmlspecialchars($item['tujuan_proyek']); ?></td>
                                                        <td>
                                                            <?php
                                                            $buktiArray = json_decode($item['bukti'], true);
                                                            if ($buktiArray) {
                                                                foreach ($buktiArray as $buktiPath) {
                                                                    echo '<a href="' . htmlspecialchars($buktiPath) . '" target="_blank">Lihat Bukti</a><br>';
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning editBtn" data-toggle="modal" data-target="#editProyekModal" data-id="<?php echo $item['id']; ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger deleteBtn" data-toggle="modal" data-target="#deleteProyekModal" data-id="<?php echo $item['id']; ?>">
                                                                <i class="fas fa-trash-alt"></i> Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="9">Tidak ada data proyek</td>
                                                </tr>
                                            <?php endif; ?>
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

    
    <!-- Add Proyek Modal -->
    <div class="modal fade" id="addProyekModal" tabindex="-1" aria-labelledby="addProyekModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addProyekForm" method="POST" action="process_add_proyek.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProyekModalLabel">Tambah Proyek</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaProyek">Nama Proyek</label>
                            <input type="text" class="form-control" id="namaProyek" name="nama_proyek" required>
                        </div>
                        <div class="form-group">
                            <label for="namaPartner">Partner</label>
                            <input type="text" class="form-control" id="namaPartner" name="partner" required>
                        </div>
                        <div class="form-group">
                            <label for="peran">Peran</label>
                            <input type="text" class="form-control" id="peran" name="peran" required>
                        </div>
                        <div class="form-group">
                            <label for="waktuAwal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="waktuAwal" name="waktu_awal" required>
                        </div>
                        <div class="form-group">
                            <label for="waktuSelesai">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="waktuSelesai" name="waktu_selesai" required>
                        </div>
                        <div class="form-group">
                            <label for="tujuanProyek">Tujuan Proyek</label>
                            <input type="text" class="form-control" id="tujuanProyek" name="tujuan_proyek" required>
                        </div>
                        <!-- <div class="form-group">
                            <label for="bukti">Bukti (upload berupa .pdf atau img, max 5mb)</label>
                            <input type="file" class="form-control-file" id="bukti" name="bukti_file"> -->
                            <label for="bukti_link">Masukkan Link Google Drive:</label>
                            <input type="url" class="form-control" id="bukti_link" name="bukti_link" placeholder="salin link g-drive yang sudah Anda buat">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Proyek Modal -->
<div class="modal fade" id="editProyekModal" tabindex="-1" aria-labelledby="editProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editProyekForm" method="POST" action="update_proyek.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProyekModalLabel">Edit Proyek</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProyekId" name="id">
                    <div class="form-group">
                        <label for="editNamaProyek">Nama Proyek</label>
                        <input type="text" class="form-control" id="editNamaProyek" name="nama_proyek" required>
                    </div>
                    <div class="form-group">
                        <label for="editNamaPartner">Nama Partner</label>
                        <input type="text" class="form-control" id="editNamaPartner" name="partner" required>
                    </div>
                    <div class="form-group">
                        <label for="editPeran">Peran</label>
                        <input type="text" class="form-control" id="editPeran" name="peran" required>
                    </div>
                    <div class="form-group">
                        <label for="editWaktuAwal">Tanggal Awal</label>
                        <input type="date" class="form-control" id="editWaktuAwal" name="waktu_awal" required>
                    </div>
                    <div class="form-group">
                        <label for="editWaktuSelesai">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="editWaktuSelesai" name="waktu_selesai" required>
                    </div>
                    <div class="form-group">
                        <label for="editTujuanProyek">Tujuan Proyek</label>
                        <input type="text" class="form-control" id="editTujuanProyek" name="tujuan_proyek" required>
                    </div>
                    <div class="form-group">
                        <label for="editBukti">Bukti</label>
                        <br>
                        <div id="currentBukti">
                            <!-- Display current bukti here -->
                        </div>
                        <label for="editBuktiLink">Masukkan Link Google Drive:</label>
                        <input type="url" class="form-control" id="editBuktiLink" name="bukti_link">
                        <small id="buktiHelp" class="form-text text-muted">Upload file bukti baru jika ingin mengganti.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <!-- Delete Proyek Modal -->
    <div class="modal fade" id="deleteProyekModal" tabindex="-1" aria-labelledby="deleteProyekModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="deleteProyekForm" method="POST" action="delete_proyek.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProyekModalLabel">Hapus Proyek</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus data proyek ini?</p>
                        <input type="hidden" id="deleteProyekId" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="script_mhs.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="../../app/plugins/jquery/jquery.min.js"></script>
<script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../app/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../app/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../app/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../app/plugins/jszip/jszip.min.js"></script>
<script src="../../app/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../app/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../app/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../app/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../app/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize DataTable
    $("#proyekTable").DataTable({
        "responsive": true,
        "autoWidth": false,
    });

    // Fill edit form with existing data
    $('.editBtn').on('click', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'get_proyek.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                $('#editProyekId').val(response.id);
                $('#editNamaProyek').val(response.nama_proyek);
                $('#editNamaPartner').val(response.partner);
                $('#editPeran').val(response.peran);
                $('#editWaktuAwal').val(response.waktu_awal);
                $('#editWaktuSelesai').val(response.waktu_selesai);
                $('#editTujuanProyek').val(response.tujuan_proyek);
                $('#editBuktiLink').val('');
            }
        });
    });
    $('.deleteBtn').on('click', function () {
        var id = $(this).data('id');
        $('#deleteProyekId').val(id);
    });
});
</script>


</body>
</html>
