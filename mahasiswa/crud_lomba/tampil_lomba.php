<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); 
}

// Fetch competition data from database
$mahasiswa_id = $_SESSION['mahasiswa_id'];
$sql = "SELECT l.id, l.nama_lomba, l.prestasi, k.nama_kategori AS kategori, t.nama AS tingkatan, l.penyelenggara, l.tanggal_pelaksanaan, l.tempat_pelaksanaan, l.bukti
        FROM lomba l
        LEFT JOIN kategori k ON l.id_kategori = k.id
        LEFT JOIN tingkatan t ON l.id_tingkatan = t.id
        WHERE l.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$mahasiswa_id]);
$lomba = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil Data Lomba</title>
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
                                    Data Lomba
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCompetitionModal">
                                        <i class="fas fa-plus-circle"></i> Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="lombaTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Lomba</th>
                                                <th>Prestasi</th>
                                                <th>Kategori</th>
                                                <th>Tingkatan</th>
                                                <th>Penyelenggara</th>
                                                <th>Tanggal</th>
                                                <th>Tempat Pelaksanaan</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lomba as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_lomba']); ?></td>
                                                    <td><?= htmlspecialchars($item['prestasi']); ?></td>
                                                    <td><?= htmlspecialchars($item['kategori']); ?></td>
                                                    <td><?= htmlspecialchars($item['tingkatan']); ?></td>
                                                    <td><?= htmlspecialchars($item['penyelenggara']); ?></td>
                                                    <td><?= htmlspecialchars($item['tanggal_pelaksanaan']); ?></td>
                                                    <td><?= htmlspecialchars($item['tempat_pelaksanaan']); ?></td>
                                                    <td>
                                                        <?php
                                                        $buktiArray = json_decode($item['bukti'], true);
                                                        foreach ($buktiArray as $buktiPath) {
                                                            echo '<a href="' . htmlspecialchars($buktiPath) . '" target="_blank">Lihat Bukti</a><br>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning editBtn" data-toggle="modal" data-target="#editCompetitionModal" data-id="<?php echo $item['id']; ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger deleteBtn" data-toggle="modal" data-target="#deleteCompetitionModal" data-id="<?php echo $item['id']; ?>">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
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

    <!-- Add Competition Modal -->
    <div class="modal fade" id="addCompetitionModal" tabindex="-1" aria-labelledby="addCompetitionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addCompetitionForm" method="POST" action="process_add_lomba.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCompetitionModalLabel">Tambah Lomba</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaLomba">Nama Lomba</label>
                            <input type="text" class="form-control" id="namaLomba" name="nama_lomba" required>
                        </div>
                        <div class="form-group">
                            <label for="idKategori">Kategori Lomba</label>
                            <select class="form-control" id="idKategori" name="id_kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                // Fetch kategori from database
                                $sql_kategori = "SELECT id, nama_kategori FROM kategori";
                                $stmt_kategori = $pdo->query($sql_kategori);
                                $kategori = $stmt_kategori->fetchAll();

                                foreach ($kategori as $kategori_item) {
                                    echo "<option value='{$kategori_item['id']}'>{$kategori_item['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="idTingkatan">Tingkatan Lomba</label>
                            <select class="form-control" id="idTingkatan" name="id_tingkatan" required>
                                <option value="">-- Pilih Tingkatan --</option>
                                <?php
                                // Fetch tingkatan from database
                                $sql_tingkatan = "SELECT id, nama FROM tingkatan";
                                $stmt_tingkatan = $pdo->query($sql_tingkatan);
                                $tingkatan = $stmt_tingkatan->fetchAll();

                                foreach ($tingkatan as $tingkatan_item) {
                                    echo "<option value='{$tingkatan_item['id']}'>{$tingkatan_item['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prestasi">Prestasi</label>
                            <input type="text" class="form-control" id="prestasi" name="prestasi" required>
                        </div>
                        <div class="form-group">
                            <label for="penyelenggara">Penyelenggara</label>
                            <input type="text" class="form-control" id="penyelenggara" name="penyelenggara" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggalPelaksanaan">Tanggal Pelaksanaan</label>
                            <input type="date" class="form-control" id="tanggalPelaksanaan" name="tanggal_pelaksanaan" required>
                        </div>
                        <div class="form-group">
                            <label for="tempatPelaksanaan">Tempat Pelaksanaan</label>
                            <input type="text" class="form-control" id="tempatPelaksanaan" name="tempat_pelaksanaan" required>
                        </div>
                        <div class="form-group">
                            <label for="bukti">Bukti (upload berupa .pdf atau img, max 5mb)</label>
                            <input type="file" class="form-control-file" id="bukti" name="bukti_file">
                            <label for="bukti_link">Atau Masukkan Link Google Drive (jika file melebihi 5mb):</label>
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

    <!-- Edit Competition Modal -->
    <div class="modal fade" id="editCompetitionModal" tabindex="-1" aria-labelledby="editCompetitionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCompetitionForm" method="POST" action="update_lomba.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCompetitionModalLabel">Edit Lomba</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id">
                        <div class="form-group">
                            <label for="editNamaLomba">Nama Lomba</label>
                            <input type="text" class="form-control" id="editNamaLomba" name="nama_lomba" required>
                        </div>
                        <div class="form-group">
                            <label for="editIdKategori">Kategori Lomba</label>
                            <select class="form-control" id="editIdKategori" name="id_kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                // Fetch kategori from database
                                foreach ($kategori as $kategori_item) {
                                    echo "<option value='{$kategori_item['id']}'>{$kategori_item['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editIdTingkatan">Tingkatan Lomba</label>
                            <select class="form-control" id="editIdTingkatan" name="id_tingkatan" required>
                                <option value="">-- Pilih Tingkatan --</option>
                                <?php
                                // Fetch tingkatan from database
                                foreach ($tingkatan as $tingkatan_item) {
                                    echo "<option value='{$tingkatan_item['id']}'>{$tingkatan_item['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editPrestasi">Prestasi</label>
                            <textarea class="form-control" id="editPrestasi" name="prestasi" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editPenyelenggara">Penyelenggara</label>
                            <input type="text" class="form-control" id="editPenyelenggara" name="penyelenggara" required>
                        </div>
                        <div class="form-group">
                            <label for="editTanggalPelaksanaan">Tanggal Pelaksanaan</label>
                            <input type="date" class="form-control" id="editTanggalPelaksanaan" name="tanggal_pelaksanaan" required>
                        </div>
                        <div class="form-group">
                            <label for="editTempatPelaksanaan">Tempat Pelaksanaan</label>
                            <input type="text" class="form-control" id="editTempatPelaksanaan" name="tempat_pelaksanaan" required>
                        </div>
                        <div class="form-group">
                            <label for="editBukti">Bukti</label>
                            <br>
                            <div id="currentBukti">
                                <!-- Display current bukti here -->
                            </div>
                            <input type="file" class="form-control-file" id="editBukti" name="bukti_file">
                            <label for="editBuktiLink">Atau Masukkan Link Google Drive:</label>
                            <input type="url" class="form-control" id="editBuktiLink" name="bukti_link">
                            <small id="buktiHelp" class="form-text text-muted">Upload file bukti baru jika ingin mengganti.</small>
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

    <!-- Delete Competition Modal -->
    <div class="modal fade" id="deleteCompetitionModal" tabindex="-1" aria-labelledby="deleteCompetitionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="deleteCompetitionForm" method="POST" action="delete_lomba.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCompetitionModalLabel">Hapus Lomba</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin menghapus data lomba ini?</p>
                        <input type="hidden" id="deleteLombaId" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Scripts -->
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
        $('#lombaTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });

        // Handle edit button click to populate data in modal
        $('.editBtn').on('click', function () {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: 'get_lomba.php',
                data: {id: id},
                dataType: 'json',
                success: function (response) {
                    $('#editId').val(response.id);
                    $('#editNamaLomba').val(response.nama_lomba);
                    $('#editIdKategori').val(response.id_kategori);
                    $('#editIdTingkatan').val(response.id_tingkatan);
                    $('#editPrestasi').val(response.prestasi);
                    $('#editPenyelenggara').val(response.penyelenggara);
                    $('#editTanggalPelaksanaan').val(response.tanggal_pelaksanaan);
                    $('#editTempatPelaksanaan').val(response.tempat_pelaksanaan);
                    $('#editBukti').val('');
                    $('#editBuktiLink').val('');
                }
            });
        });

        // Set competition ID for deletion
        $('.deleteBtn').on('click', function () {
            var id = $(this).data('id');
            $('#deleteLombaId').val(id);
        });
    });
</script>
</body>
</html>
