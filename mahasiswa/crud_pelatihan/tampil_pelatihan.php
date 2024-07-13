<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa' || !isset($_SESSION['mahasiswa_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); 
}

$mahasiswa_id = $_SESSION['mahasiswa_id'];
$sql = "SELECT p.id_pelatihan, p.nama_pelatihan, p.materi, p.deskripsi, p.tanggal_mulai, p.tanggal_selesai, t.nama AS tingkatan, p.tempat_pelaksanaan, p.penyelenggara, p.bukti
        FROM pelatihan p
        LEFT JOIN tingkatan t ON p.id_tingkatan = t.id
        WHERE p.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$mahasiswa_id]);
$pelatihan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil Data Pelatihan</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        nav-sidebar .nav-link.active {
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
        .table-responsive {
            overflow-x: auto;
            max-height: 400px;
        }
        .table-responsive thead {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #343a40;
        }
        .table-responsive thead th {
            color: #ffffff;
            border-color: #454d55; 
        }
        .table-responsive tbody tr:hover {
            background-color: #f2f2f2; 
        }
        .table th, .table td {
            white-space: nowrap;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'navbar_mhs.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
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

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-award nav-icon"></i>
                                    Data Pelatihan
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPelatihanModal">
                                        <i class="fas fa-plus-circle"></i> Tambah 
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="pelatihanTable" class="table table-bordered table-striped nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Materi</th>
                                                <th>Deskripsi</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Tingkatan</th>
                                                <th>Tempat</th>
                                                <th>Penyelenggara</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pelatihan as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_pelatihan']); ?></td>
                                                    <td><?= htmlspecialchars($item['materi']); ?></td>
                                                    <td><?= htmlspecialchars($item['deskripsi']); ?></td>
                                                    <td><?= htmlspecialchars($item['tanggal_mulai']); ?></td>
                                                    <td><?= htmlspecialchars($item['tanggal_selesai']); ?></td>
                                                    <td><?= htmlspecialchars($item['tingkatan']); ?></td>
                                                    <td><?= htmlspecialchars($item['tempat_pelaksanaan']); ?></td>
                                                    <td><?= htmlspecialchars($item['penyelenggara']); ?></td>
                                                    <td>
                                                        <?php
                                                        $buktiArray = json_decode($item['bukti'], true);
                                                        if (is_array($buktiArray)) {
                                                            foreach ($buktiArray as $buktiPath) {
                                                                echo '<a href="' . htmlspecialchars($buktiPath) . '" target="_blank">Lihat Bukti</a><br>';
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning editBtn" data-toggle="modal" data-target="#editPelatihanModal" data-id="<?= $item['id_pelatihan']; ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger deleteBtn" data-toggle="modal" data-target="#deletePelatihanModal" data-id="<?= $item['id_pelatihan']; ?>">
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

    <!-- Add Pelatihan Modal -->
    <div class="modal fade" id="addPelatihanModal" tabindex="-1" aria-labelledby="addPelatihanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addPelatihanForm" method="POST" action="process_add_pelatihan.php" enctype="multipart/form-data" onsubmit="return validateForm('add')">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPelatihanModalLabel">Tambah Pelatihan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namaPelatihan">Nama Pelatihan</label>
                            <input type="text" class="form-control" id="namaPelatihan" name="nama_pelatihan" required>
                        </div>
                        <div class="form-group">
                            <label for="materi">Materi</label>
                            <textarea class="form-control" id="materi" name="materi" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tanggalMulai">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggalMulai" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggalSelesai">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tanggalSelesai" name="tanggal_selesai" required>
                        </div>
                        <div class="form-group">
                            <label for="idTingkatan">Tingkatan</label>
                            <select class="form-control" id="idTingkatan" name="id_tingkatan" required>
                                <option value="">-- Pilih Tingkatan --</option>
                                <?php
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
                            <label for="tempatPelaksanaan">Tempat Pelaksanaan</label>
                            <input type="text" class="form-control" id="tempatPelaksanaan" name="tempat_pelaksanaan" required>
                        </div>
                        <div class="form-group">
                            <label for="penyelenggara">Penyelenggara</label>
                            <input type="text" class="form-control" id="penyelenggara" name="penyelenggara" required>
                        </div>
                        <div class="form-group">
                            <label for="bukti">Bukti (upload berupa .pdf atau img, max 5mb)</label>
                            <input type="file" class="form-control-file" id="bukti_file" name="bukti_file" accept=".pdf,.jpg,.jpeg,.png">
                            <label for="bukti_link">Atau Masukkan Link Google Drive (jika file melebihi 5mb):</label>
                            <input type="url" class="form-control" id="bukti_link" name="bukti_link" placeholder="salin link g-drive yang sudah Anda buat">
                        </div>
                        <div id="addValidationMessage" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Pelatihan Modal -->
    <div class="modal fade" id="editPelatihanModal" tabindex="-1" aria-labelledby="editPelatihanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editPelatihanForm" method="POST" action="update_pelatihan.php" enctype="multipart/form-data" onsubmit="return validateForm('edit')">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPelatihanModalLabel">Edit Pelatihan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editPelatihanId" name="id">
                        <div class="form-group">
                            <label for="editNamaPelatihan">Nama Pelatihan</label>
                            <input type="text" class="form-control" id="editNamaPelatihan" name="nama_pelatihan" required>
                        </div>
                        <div class="form-group">
                            <label for="editMateri">Materi</label>
                            <textarea class="form-control" id="editMateri" name="materi" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editDeskripsi">Deskripsi</label>
                            <textarea class="form-control" id="editDeskripsi" name="deskripsi" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editTanggalMulai">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="editTanggalMulai" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="editTanggalSelesai">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="editTanggalSelesai" name="tanggal_selesai" required>
                        </div>
                        <div class="form-group">
                            <label for="editIdTingkatan">Tingkatan</label>
                            <select class="form-control" id="editIdTingkatan" name="id_tingkatan" required>
                                <option value="">-- Pilih Tingkatan --</option>
                                <?php
                                foreach ($tingkatan as $tingkatan_item) {
                                    echo "<option value='{$tingkatan_item['id']}'>{$tingkatan_item['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editTempatPelaksanaan">Tempat Pelaksanaan</label>
                            <input type="text" class="form-control" id="editTempatPelaksanaan" name="tempat_pelaksanaan" required>
                        </div>
                        <div class="form-group">
                            <label for="editPenyelenggara">Penyelenggara</label>
                            <input type="text" class="form-control" id="editPenyelenggara" name="penyelenggara" required>
                        </div>
                        <div class="form-group">
                            <label for="editBukti">Bukti</label>
                            <br>
                            <div id="currentBukti">
                                <!-- Display current bukti here -->
                            </div>
                            <input type="file" class="form-control-file" id="editBukti_file" name="bukti_file" accept=".pdf,.jpg,.jpeg,.png">
                            <label for="editBuktiLink">Atau Masukkan Link Google Drive:</label>
                            <input type="url" class="form-control" id="editBuktiLink" name="bukti_link">
                            <small id="buktiHelp" class="form-text text-muted">Upload file bukti baru jika ingin mengganti.</small>
                        </div>
                        <div id="editValidationMessage" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Pelatihan Modal -->
    <div class="modal fade" id="deletePelatihanModal" tabindex="-1" aria-labelledby="deletePelatihanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="deletePelatihanForm" method="POST" action="delete_pelatihan.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePelatihanModalLabel">Hapus Pelatihan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus pelatihan ini?</p>
                        <input type="hidden" id="deletePelatihanId" name="id">
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
    $('#pelatihanTable').DataTable();

    // Handle edit button click to populate data in modal
    $('.editBtn').on('click', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'get_pelatihan.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                $('#editPelatihanId').val(response.id_pelatihan);
                $('#editNamaPelatihan').val(response.nama_pelatihan);
                $('#editMateri').val(response.materi);
                $('#editDeskripsi').val(response.deskripsi);
                $('#editTanggalMulai').val(response.tanggal_mulai);
                $('#editTanggalSelesai').val(response.tanggal_selesai);
                $('#editIdTingkatan').val(response.id_tingkatan);
                $('#editTempatPelaksanaan').val(response.tempat_pelaksanaan);
                $('#editPenyelenggara').val(response.penyelenggara);
                $('#editBukti_file').val('');
                $('#editBuktiLink').val('');

                var currentBuktiHtml = '';
                if (response.bukti) {
                    var buktiArray = JSON.parse(response.bukti);
                    if (Array.isArray(buktiArray)) {
                        buktiArray.forEach(function (buktiPath) {
                            currentBuktiHtml += '<a href="' + buktiPath + '" target="_blank">Lihat Bukti</a><br>';
                        });
                    } else {
                        currentBuktiHtml = '<a href="' + response.bukti + '" target="_blank">Lihat Bukti</a>';
                    }
                }
                $('#currentBukti').html(currentBuktiHtml);
            }
        });
    });

    // Set pelatihan ID for deletion
    $('.deleteBtn').on('click', function () {
        var id = $(this).data('id');
        $('#deletePelatihanId').val(id);
    });

    // Custom validation for add form
    $('#addPelatihanForm').on('submit', function () {
        return validateForm('add');
    });

    // Custom validation for edit form
    $('#editPelatihanForm').on('submit', function () {
        return validateForm('edit');
    });

    function validateForm(type) {
        let fileInput, linkInput, validationMessage;
        if (type === 'add') {
            fileInput = document.getElementById('bukti_file');
            linkInput = document.getElementById('bukti_link');
            validationMessage = document.getElementById('addValidationMessage');
        } else if (type === 'edit') {
            fileInput = document.getElementById('editBukti_file');
            linkInput = document.getElementById('editBuktiLink');
            validationMessage = document.getElementById('editValidationMessage');
        }

        validationMessage.innerHTML = '';  // Clear previous messages

        if (fileInput.files.length > 0 && linkInput.value !== "") {
            validationMessage.innerHTML = "Silakan upload salah satu bukti saja: file atau link Google Drive.";
            return false;
        }

        if (fileInput.files.length === 0 && linkInput.value === "") {
            validationMessage.innerHTML = "Silakan upload salah satu bukti: file atau link Google Drive.";
            return false;
            }

            return true;
        }
    });
</script>
</body>
</html>
