<?php
session_start();
include '../../includes/db.php';

// Redirect to login if not logged in as a student
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
//     header('Location: ../../auth/login.php');
//     exit;
// }

// Fetch jurusans from database
$sql_jurusans = "SELECT id, nama_jurusan FROM jurusans";
$stmt_jurusans = $pdo->query($sql_jurusans);
$jurusans = $stmt_jurusans->fetchAll();

// Check for success message notification
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear notification after displaying
}

// Fetch competency data from database
$sql = "SELECT k.id, k.nama_kompetensi, k.nomor_sk, k.tahun_sertifikasi, k.masa_berlaku, k.bukti, k.jurusan_id, k.prodi_id, j.nama_jurusan, p.nama_prodi
        FROM kompetensi k
        LEFT JOIN prodis p ON k.prodi_id = p.id
        LEFT JOIN jurusans j ON k.jurusan_id = j.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$kompetensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil Data Kompetensi</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
                        <h1 class="m-0">Tampil Data Kompetensi</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Tampil Data Kompetensi</li>
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
                                    Data Kompetensi
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
                                                <th>Nama Kompetensi</th>
                                                <th>Nomor SK</th>
                                                <th>Tahun Sertifikasi</th>
                                                <th>Masa Berlaku</th>
                                                <th>Prodi</th>
                                                <th>Jurusan</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($kompetensi as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_kompetensi']); ?></td>
                                                    <td><?= htmlspecialchars($item['nomor_sk']); ?></td>
                                                    <td><?= htmlspecialchars($item['tahun_sertifikasi']); ?></td>
                                                    <td><?= htmlspecialchars($item['masa_berlaku']); ?></td>
                                                    <td><?= htmlspecialchars($item['nama_prodi']); ?></td>
                                                    <td><?= htmlspecialchars($item['nama_jurusan']); ?></td>
                                                    <td>
                                                        <?php
                                                        $buktiArray = json_decode($item['bukti'], true);
                                                        foreach ($buktiArray as $buktiPath) {
                                                            echo '<a href="' . $buktiPath . '" target="_blank">Lihat Bukti</a><br>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning editBtn" data-toggle="modal" data-target="#editCompetencyModal" data-id="<?php echo $item['id']; ?>">Edit</button>
                                                        <button class="btn btn-sm btn-danger deleteBtn" data-toggle="modal" data-target="#deleteCompetencyModal" data-id="<?php echo $item['id']; ?>">Hapus</button>
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

    <!-- Add Competency Modal -->
    <div class="modal fade" id="addCompetencyModal" tabindex="-1" aria-labelledby="addCompetencyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addCompetencyForm" method="POST" action="process_add_kompetensi.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCompetencyModalLabel">Tambah Kompetensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaKompetensi">Nama Kompetensi</label>
                            <input type="text" class="form-control" id="namaKompetensi" name="nama_kompetensi" required>
                        </div>
                        <div class="form-group">
                            <label for="jurusan">Jurusan</label>
                            <select class="form-control" id="jurusan" name="jurusan" required>
                                <option value="">-- Pilih Jurusan --</option>
                                <?php foreach ($jurusans as $jurusan): ?>
                                    <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama_jurusan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prodi">Prodi</label>
                            <select class="form-control" id="prodi" name="prodi" required>
                                <option value="">-- Pilih Prodi --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nomorSk">Nomor SK</label>
                            <input type="text" class="form-control" id="nomorSk" name="nomor_sk" required>
                        </div>
                        <div class="form-group">
                            <label for="tahunSertifikasi">Tahun Sertifikasi</label>
                            <input type="number" class="form-control" id="tahunSertifikasi" name="tahun_sertifikasi" required>
                        </div>
                        <div class="form-group">
                            <label for="masaBerlaku">Masa Berlaku</label>
                            <input type="number" class="form-control" id="masaBerlaku" name="masa_berlaku" required>
                        </div>
                        <div class="form-group">
                            <label for="bukti">Bukti</label>
                            <input type="file" class="form-control-file" id="bukti" name="bukti[]" multiple required>
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

    <!-- Edit Competency Modal -->
    <div class="modal fade" id="editCompetencyModal" tabindex="-1" aria-labelledby="editCompetencyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCompetencyForm" method="POST" action="update_kompetensi.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCompetencyModalLabel">Edit Kompetensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editKompetensiId" name="id">
                        <div class="form-group">
                            <label for="editNamaKompetensi">Nama Kompetensi</label>
                            <input type="text" class="form-control" id="editNamaKompetensi" name="nama_kompetensi" required>
                        </div>
                        <div class="form-group">
                            <label for="editJurusan">Jurusan</label>
                            <select class="form-control" id="editJurusan" name="jurusan_id" required>
                                <option value="">-- Pilih Jurusan --</option>
                                <?php foreach ($jurusans as $jurusan): ?>
                                    <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama_jurusan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editProdi">Prodi</label>
                            <select class="form-control" id="editProdi" name="prodi_id" required>
                                <option value="">-- Pilih Prodi --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editNomorSk">Nomor SK</label>
                            <input type="text" class="form-control" id="editNomorSk" name="nomor_sk" required>
                        </div>
                        <div class="form-group">
                            <label for="editTahunSertifikasi">Tahun Sertifikasi</label>
                            <input type="number" class="form-control" id="editTahunSertifikasi" name="tahun_sertifikasi" required>
                        </div>
                        <div class="form-group">
                            <label for="editMasaBerlaku">Masa Berlaku</label>
                            <input type="number" class="form-control" id="editMasaBerlaku" name="masa_berlaku" required>
                        </div>
                        <div class="form-group">
                            <label for="editBukti">Bukti</label>
                            <br>
                            <?php if (!empty($item['bukti'])) : ?>
                                <?php
                                $buktiPaths = json_decode($item['bukti'], true);
                                foreach ($buktiPaths as $path) {
                                    $fileName = basename($path);
                                    echo "<a href='../../$path' target='_blank'>$fileName</a><br>";
                                }
                                ?>
                            <?php else: ?>
                                <span>Tidak ada bukti yang tersedia.</span>
                            <?php endif; ?>
                            <br>
                            <input type="file" class="form-control-file" id="editBukti" name="bukti[]" multiple>
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

    <!-- Delete Competency Modal -->
    <div class="modal fade" id="deleteCompetencyModal" tabindex="-1" aria-labelledby="deleteCompetencyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="deleteCompetencyForm" method="POST" action="delete_kompetensi.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCompetencyModalLabel">Hapus Kompetensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus kompetensi ini?</p>
                        <input type="hidden" id="deleteKompetensiId" name="id">
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
    $("#kompetensiTable").DataTable({
        "responsive": true,
        "autoWidth": false,
    });

    // Load prodi options based on selected jurusan
    $('#jurusan').change(function() {
        var jurusanId = $(this).val();
        $.ajax({
            url: 'get_prodi.php',
            type: 'POST',
            data: { jurusan_id: jurusanId },
            dataType: 'json',
            success: function(response) {
                var prodiSelect = $('#prodi');
                prodiSelect.empty();
                prodiSelect.append('<option value="">-- Pilih Prodi --</option>');
                $.each(response, function(key, value) {
                    prodiSelect.append('<option value="' + value.id + '">' + value.nama_prodi + '</option>');
                });
            }
        });
    });

    // Load prodi options for edit form based on selected jurusan
    $('#editJurusan').change(function() {
        var jurusanId = $(this).val();
        $.ajax({
            url: 'get_prodi.php',
            type: 'POST',
            data: { jurusan_id: jurusanId },
            dataType: 'json',
            success: function(response) {
                var prodiSelect = $('#editProdi');
                prodiSelect.empty();
                prodiSelect.append('<option value="">-- Pilih Prodi --</option>');
                $.each(response, function(key, value) {
                    prodiSelect.append('<option value="' + value.id + '">' + value.nama_prodi + '</option>');
                });
            }
        });
    });

    // Fill edit form with existing data
    $('.editBtn').on('click', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'get_kompetensi.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                $('#editKompetensiId').val(response.id);
                $('#editNamaKompetensi').val(response.nama_kompetensi);
                $('#editJurusan').val(response.jurusan_id).trigger('change');
                setTimeout(function () {
                    $('#editProdi').val(response.prodi_id);
                }, 500);
                $('#editNomorSk').val(response.nomor_sk);
                $('#editTahunSertifikasi').val(response.tahun_sertifikasi);
                $('#editMasaBerlaku').val(response.masa_berlaku);
            }
        });
    });

    // Set competency ID for deletion
    $('.deleteBtn').on('click', function () {
        var id = $(this).data('id');
        $('#deleteKompetensiId').val(id);
    });

    // Trigger change event on page load for edit form to load prodi options
    if ($('#editJurusan').val() !== '') {
        $('#editJurusan').trigger('change');
    }
});
</script>
