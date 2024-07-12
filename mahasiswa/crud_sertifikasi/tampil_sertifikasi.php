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

// Fetch competency data from database
$mahasiswa_id = $_SESSION['mahasiswa_id'];

// Debugging output
echo "Session Mahasiswa ID: $mahasiswa_id<br>";

$sql = "SELECT s.id, s.nama_sertifikasi, s.nomor_sk, s.lembaga_id, p.nama_lembaga, s.tanggal_diperoleh, s.tanggal_kadaluarsa, s.bukti
        FROM sertifikasi s
        LEFT JOIN penyelenggara_sertifikasi p ON s.lembaga_id = p.id
        WHERE s.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$mahasiswa_id]);
$sertifikasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging output untuk hasil query
if (empty($sertifikasi)) {
    echo "Error: Sertifikasi data tidak ditemukan untuk Mahasiswa ID: $mahasiswa_id<br>";
} else {
    echo "Data Sertifikasi ditemukan untuk Mahasiswa ID: $mahasiswa_id<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil Data Sertifikasi</title>
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
                                    Data Sertifikasi
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCompetencyModal">
                                        <i class="fas fa-plus-circle"></i> Tambah 
                                    </button>
                                    <!-- Buttons for PDF and Excel -->
                                    <!-- <button class="btn btn-danger ml-2" id="pdfButton">
                                        <i class="far fa-file-pdf"></i> PDF
                                    </button>
                                    <button class="btn btn-success ml-2" id="excelButton">
                                        <i class="far fa-file-excel"></i> Excel
                                    </button> -->
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="kompetensiTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Sertifikasi</th>
                                                <th>Nomor SK</th>
                                                <th>Lembaga Penerbit</th>
                                                <th>Tanggal Diperoleh</th>
                                                <th>Tanggal Kadaluarsa</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sertifikasi as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_sertifikasi']); ?></td>
                                                    <td><?= htmlspecialchars($item['nomor_sk']); ?></td>
                                                    <td><?= htmlspecialchars($item['nama_lembaga']); ?></td>
                                                    <td><?= htmlspecialchars($item['tanggal_diperoleh']); ?></td>
                                                    <td><?= htmlspecialchars($item['tanggal_kadaluarsa']); ?></td>
                                                    <td>
                                                        <?php
                                                        $buktiArray = json_decode($item['bukti'], true);
                                                        if (is_array($buktiArray)) {
                                                            foreach ($buktiArray as $buktiPath) {
                                                                echo '<a href="' . htmlspecialchars($buktiPath) . '" target="_blank">Lihat Bukti</a><br>';
                                                            }
                                                        } else {
                                                            echo '<a href="' . htmlspecialchars($item['bukti']) . '" target="_blank">Lihat Bukti</a>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning editBtn" data-toggle="modal" data-target="#editCompetencyModal" data-id="<?php echo $item['id']; ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger deleteBtn" data-toggle="modal" data-target="#deleteCompetencyModal" data-id="<?php echo $item['id']; ?>">
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

    <!-- Add Competency Modal -->
    <div class="modal fade" id="addCompetencyModal" tabindex="-1" aria-labelledby="addCompetencyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addCompetencyForm" method="POST" action="process_add_sertifikasi.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCompetencyModalLabel">Tambah Sertifikasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="mahasiswa_id" value="<?php echo htmlspecialchars($_SESSION['mahasiswa_id']); ?>">
                        <div class="form-group">
                            <label for="namaSertifikasi">Nama Sertifikasi</label>
                            <input type="text" class="form-control" id="namaSertifikasi" name="nama_sertifikasi" required>
                        </div>
                        <div class="form-group">
                            <label for="lembagaId">Lembaga Penerbit</label>
                            <select class="form-control" id="lembagaId" name="lembaga_id" required>
                                <option value="">-- Pilih Lembaga --</option>
                                <?php
                                $sql_lembaga = "SELECT id, nama_lembaga FROM penyelenggara_sertifikasi";
                                $stmt_lembaga = $pdo->query($sql_lembaga);
                                $lembaga = $stmt_lembaga->fetchAll();
                                foreach ($lembaga as $lembaga_item) {
                                    echo "<option value='" . htmlspecialchars($lembaga_item['id']) . "'>" . htmlspecialchars($lembaga_item['nama_lembaga']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nomorSk">Nomor SK</label>
                            <input type="text" class="form-control" id="nomorSk" name="nomor_sk" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggalDiperoleh">Tanggal Diperoleh</label>
                            <input type="date" class="form-control" id="tanggalDiperoleh" name="tanggal_diperoleh" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggalKadaluarsa">Tanggal Kadaluarsa</label>
                            <input type="date" class="form-control" id="tanggalKadaluarsa" name="tanggal_kadaluarsa">
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

    <!-- Edit Competency Modal -->
    <div class="modal fade" id="editCompetencyModal" tabindex="-1" aria-labelledby="editCompetencyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCompetencyForm" method="POST" action="update_sertifikasi.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCompetencyModalLabel">Edit Sertifikasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editKompetensiId" name="id">
                        <div class="form-group">
                            <label for="editNamaKompetensi">Nama Sertifikasi</label>
                            <input type="text" class="form-control" id="editNamaKompetensi" name="nama_sertifikasi" required>
                        </div>
                        <div class="form-group">
                            <label for="editLembagaId">Lembaga Penerbit</label>
                            <select class="form-control" id="editLembagaId" name="lembaga_id" required>
                                <option value="">-- Pilih Lembaga --</option>
                                <?php
                                foreach ($lembaga as $lembaga_item) {
                                    echo "<option value='" . htmlspecialchars($lembaga_item['id']) . "'>" . htmlspecialchars($lembaga_item['nama_lembaga']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editNomorSk">Nomor SK</label>
                            <input type="number" class="form-control" id="editNomorSk" name="nomor_sk" required>
                        </div>
                        <div class="form-group">
                            <label for="editTanggalDiperoleh">Tanggal Diperoleh</label>
                            <input type="date" class="form-control" id="editTanggalDiperoleh" name="tanggal_diperoleh" required>
                        </div>
                        <div class="form-group">
                            <label for="editTanggalKadaluarsa">Tanggal Kadaluarsa</label>
                            <input type="date" class="form-control" id="editTanggalKadaluarsa" name="tanggal_kadaluarsa">
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
            <form id="deleteCompetencyForm" method="POST" action="delete_sertifikasi.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCompetencyModalLabel">Hapus Sertifikasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus sertifikasi ini?</p>
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
    var table = $("#kompetensiTable").DataTable({
        "responsive": true,
        "autoWidth": false
    });

    // Fill edit form with existing data
    $('.editBtn').on('click', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'get_sertifikasi.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                $('#editKompetensiId').val(response.id);
                $('#editNamaKompetensi').val(response.nama_sertifikasi);
                $('#editLembagaId').val(response.lembaga_id);
                $('#editNomorSk').val(response.nomor_sk);
                $('#editTanggalDiperoleh').val(response.tanggal_diperoleh);
                $('#editTanggalKadaluarsa').val(response.tanggal_kadaluarsa);
                $('#editBukti').val('');
                $('#editBuktiLink').val('');
                // Update currentBukti section
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

    // Set competency ID for deletion
    $('.deleteBtn').on('click', function () {
        var id = $(this).data('id');
        $('#deleteKompetensiId').val(id);
    });
});
</script>
</body>
</html>
