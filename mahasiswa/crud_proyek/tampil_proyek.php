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

// Fetch project data from the database
$mahasiswa_id = $_SESSION['mahasiswa_id'];
$sql = "SELECT p.id, p.nama_proyek, p.partner, p.peran, p.waktu_awal, p.waktu_selesai, p.tujuan_proyek, p.bukti
        FROM proyek p
        WHERE p.mahasiswa_id = ?";
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
                                <h3 class="card-title"><i class="fas fa-project-diagram nav-icon"></i> Data Proyek</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProyekModal">
                                        <i class="fas fa-plus-circle"></i> Tambah 
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="proyekTable" class="table table-bordered table-striped nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Proyek</th>
                                                <th>Klien</th>
                                                <th>Peran</th>
                                                <th>Waktu Awal</th>
                                                <th>Waktu Selesai</th>
                                                <th>Tujuan Proyek</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                                        foreach ($buktiArray as $buktiPath) {
                                                            echo '<a href="' . htmlspecialchars($buktiPath) . '" target="_blank">Lihat Bukti</a><br>';
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Proyek Modal -->
<div class="modal fade" id="addProyekModal" tabindex="-1" aria-labelledby="addProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addProyekForm" method="POST" action="process_add_proyek.php">
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
                        <label for="partner">Klien</label>
                        <input type="text" class="form-control" id="partner" name="partner" required>
                    </div>
                    <div class="form-group">
                        <label for="peran">Peran</label>
                        <input type="text" class="form-control" id="peran" name="peran" required>
                    </div>
                    <div class="form-group">
                        <label for="waktuAwal">Waktu Awal</label>
                        <input type="date" class="form-control" id="waktuAwal" name="waktu_awal" required>
                    </div>
                    <div class="form-group">
                        <label for="waktuSelesai">Waktu Selesai</label>
                        <input type="date" class="form-control" id="waktuSelesai" name="waktu_selesai" required>
                    </div>
                    <div class="form-group">
                        <label for="tujuanProyek">Tujuan Proyek</label>
                        <textarea class="form-control" id="tujuanProyek" name="tujuan_proyek" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bukti_link">Masukkan Link Google Drive</label>
                        <input type="url" class="form-control" id="bukti_link" name="bukti_link" placeholder="Salin link Google Drive yang sudah Anda buat" required>
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
            <form id="editProyekForm" method="POST" action="update_proyek.php">
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
                        <label for="editPartner">Klien</label>
                        <input type="text" class="form-control" id="editPartner" name="partner" required>
                    </div>
                    <div class="form-group">
                        <label for="editPeran">Peran</label>
                        <input type="text" class="form-control" id="editPeran" name="peran" required>
                    </div>
                    <div class="form-group">
                        <label for="editWaktuAwal">Waktu Awal</label>
                        <input type="date" class="form-control" id="editWaktuAwal" name="waktu_awal" required>
                    </div>
                    <div class="form-group">
                        <label for="editWaktuSelesai">Waktu Selesai</label>
                        <input type="date" class="form-control" id="editWaktuSelesai" name="waktu_selesai" required>
                    </div>
                    <div class="form-group">
                        <label for="editTujuanProyek">Tujuan Proyek</label>
                        <textarea class="form-control" id="editTujuanProyek" name="tujuan_proyek" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editBukti">Bukti Saat Ini</label>
                        <br>
                        <div id="currentBukti">
                            <!-- Display current bukti here -->
                        </div>
                        <label for="editBuktiLink">Masukkan Link Google Drive Baru:</label>
                        <input type="url" class="form-control" id="editBuktiLink" name="bukti_link" required>
                        <small id="buktiHelp" class="form-text text-muted">Masukkan link Google Drive baru jika ingin mengganti bukti.</small>
                        <div id="editValidationMessage" class="text-danger"></div>
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
                        <p>Apakah Anda yakin ingin menghapus proyek ini?</p>
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
    $('#proyekTable').DataTable();

    // Handle edit button click to populate data in modal
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
                $('#editPartner').val(response.partner);
                $('#editPeran').val(response.peran);
                $('#editWaktuAwal').val(response.waktu_awal);
                $('#editWaktuSelesai').val(response.waktu_selesai);
                $('#editTujuanProyek').val(response.tujuan_proyek);
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

    // Set proyek ID for deletion
    $('.deleteBtn').on('click', function () {
        var id = $(this).data('id');
        $('#deleteProyekId').val(id);
    });

    // Custom validation for add form
    $('#addProyekForm').on('submit', function () {
        return validateForm('add');
    });

    // Custom validation for edit form
    $('#editProyekForm').on('submit', function () {
        return validateForm('edit');
    });

    function validateForm(type) {
        let linkInput, validationMessage;
        if (type === 'add') {
            linkInput = document.getElementById('bukti_link');
            validationMessage = document.getElementById('addValidationMessage');
        } else if (type === 'edit') {
            linkInput = document.getElementById('editBuktiLink');
            validationMessage = document.getElementById('editValidationMessage');
        }

        validationMessage.innerHTML = '';  // Clear previous messages

        if (linkInput.value === "") {
            validationMessage.innerHTML = "Silakan masukkan link Google Drive.";
            return false;
        }

        return true;
    }
});
</script>
</body>
</html>

