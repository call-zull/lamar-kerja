<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Function to fetch all jobs from the database
function fetchJobs($pdo) {
    try {
        $sql = "SELECT lowongan_kerja.*, prodis.nama_prodi FROM lowongan_kerja 
                LEFT JOIN prodis ON FIND_IN_SET(prodis.id, lowongan_kerja.prodi_id)";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching jobs: " . $e->getMessage();
        return false;
    }
}

// Fetching jobs
$jobs = fetchJobs($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perusahaan Dashboard</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.9.7/dist/tagify.css">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .content-wrapper {
            margin-top: 20px;
        }
        .table-responsive {
            overflow-y: auto;
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
   
        .tagify__tag__removeBtn {
            color: #fff;
            margin-left: 5px;
        }
        .input-group .form-control {
            border-right: 0;
        }
        .input-group-append .btn {
            border-left: 0;
        }
    
        .tag-input {
        min-height: 38px;
        display: flex;
        flex-wrap: wrap;
        }

    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <?php include 'navbar_perusahaan.php'; ?>
    
    <!-- Content Wrapper. Contains page content -->
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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-briefcase"></i> Lowongan Pekerjaan</h3>
                        <div class="card-tools">
                            <button class="btn btn-primary add-button" data-toggle="modal" data-target="#modalTambah">
                                <i class="fas fa-plus-circle"></i> Tambah
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="lowonganTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pekerjaan</th>
                                        <th>Posisi</th>
                                        <th>Kualifikasi</th>
                                        <th>Prodi</th>
                                        <th>Keahlian</th>
                                        <th>Tanggal Posting</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($jobs) {
                                        $no = 1;
                                        foreach ($jobs as $row) {
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_pekerjaan']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['posisi']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['kualifikasi']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_prodi']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['keahlian']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tanggal_posting']) . "</td>";
                                            echo "<td>
                                                    <button class='btn btn-sm btn-warning editBtn' data-toggle='modal' data-target='#modalEdit' 
                                                        data-id='" . $row['id'] . "' 
                                                        data-nama='" . htmlspecialchars($row['nama_pekerjaan']) . "' 
                                                        data-posisi='" . htmlspecialchars($row['posisi']) . "'
                                                        data-kualifikasi='" . htmlspecialchars($row['kualifikasi']) . "'
                                                        data-prodi_id='" . htmlspecialchars($row['prodi_id']) . "'
                                                        data-keahlian='" . htmlspecialchars($row['keahlian']) . "'>Edit</button>
                                                    <button class='btn btn-sm btn-danger deleteBtn' data-toggle='modal' data-target='#modalHapus' data-id='" . $row['id'] . "'>Hapus</button>
                                                    <button class='btn btn-sm btn-info lihatPelamarBtn' data-toggle='modal' data-target='#modalPelamar' data-id='" . $row['id'] . "'>Lihat Pelamar</button>
                                                </td>";
                                            echo "</tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>Tidak ada data</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="process_add_job.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Lowongan Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_pekerjaan">Nama Pekerjaan</label>
                            <input type="text" class="form-control" id="nama_pekerjaan" name="nama_pekerjaan" required>
                        </div>
                        <div class="form-group">
                            <label for="posisi">Posisi</label>
                            <input type="text" class="form-control" id="posisi" name="posisi" required>
                        </div>
                        <div class="form-group">
                            <label for="kualifikasi">Kualifikasi</label>
                            <input type="text" class="form-control" id="kualifikasi" name="kualifikasi" required>
                        </div>
                        <div class="form-group">
                            <label for="prodi_id">Prodi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="prodi_id" name="prodi_id[]" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="addProdiButton">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="keahlian">Keahlian</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="keahlian" name="keahlian" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="addTagButton">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="process_edit_job.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Lowongan Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="edit_id">
                        <div class="form-group">
                            <label for="edit_nama_pekerjaan">Nama Pekerjaan</label>
                            <input type="text" class="form-control" id="edit_nama_pekerjaan" name="edit_nama_pekerjaan" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_posisi">Posisi</label>
                            <input type="text" class="form-control" id="edit_posisi" name="edit_posisi" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_kualifikasi">Kualifikasi</label>
                            <input type="text" class="form-control" id="edit_kualifikasi" name="edit_kualifikasi" required>
                        </div>
                        <div class="form-group">
    <label for="edit_prodi_id">Prodi</label>
    <div class="input-group">
        <input type="text" class="form-control" id="edit_prodi_id" name="edit_prodi_id[]" required>
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary" id="editAddProdiButton">+</button>
        </div>
    </div>
</div>
                        <div class="form-group">
                            <label for="edit_keahlian">Keahlian</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit_keahlian" name="edit_keahlian" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="editAddTagButton">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="process_delete_job.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHapusLabel">Hapus Lowongan Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hapus_id" name="hapus_id">
                        <p>Apakah Anda yakin ingin menghapus lowongan ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Pelamar -->
    <div class="modal fade" id="modalPelamar" tabindex="-1" aria-labelledby="modalPelamarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPelamarLabel">Daftar Pelamar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelamar</th>
                                <th>Email</th>
                                <th>No Telepon</th>
                                <th>Prodi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pelamarTableBody">
                            <!-- Data pelamar akan dimuat di sini melalui AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./wrapper -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.9.7/dist/tagify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="../app/plugins/fontawesome-free/js/all.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="../app/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../app/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../app/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../app/plugins/jszip/jszip.min.js"></script>
<script src="../app/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../app/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../app/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../app/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../app/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
    $(document).ready(function() {
        $('#lowonganTable').DataTable();

        var keahlianInput = document.querySelector('#keahlian');
        var tagifyKeahlian = new Tagify(keahlianInput);

        var editKeahlianInput = document.querySelector('#edit_keahlian');
        var tagifyEditKeahlian = new Tagify(editKeahlianInput);

        $('#addTagButton').on('click', function() {
            tagifyKeahlian.addEmptyTag();
        });

        $('#editAddTagButton').on('click', function() {
            tagifyEditKeahlian.addEmptyTag();
        });

        // Initialize Tagify for Prodi input
        var prodiInput = document.querySelector('#prodi_id');
        var tagifyProdi = new Tagify(prodiInput, {
            whitelist: [<?php
                $sql_prodi = "SELECT nama_prodi FROM prodis";
                $stmt_prodi = $pdo->query($sql_prodi);
                $prodiArray = [];
                while ($prodi = $stmt_prodi->fetch(PDO::FETCH_ASSOC)) {
                    $prodiArray[] = "'" . $prodi['nama_prodi'] . "'";
                }
                echo implode(",", $prodiArray);
            ?>],
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: false
            }
        });

        var editProdiInput = document.querySelector('#edit_prodi_id');
        var tagifyEditProdi = new Tagify(editProdiInput, {
            whitelist: [<?php
                $sql_prodi = "SELECT nama_prodi FROM prodis";
                $stmt_prodi = $pdo->query($sql_prodi);
                $prodiArray = [];
                while ($prodi = $stmt_prodi->fetch(PDO::FETCH_ASSOC)) {
                    $prodiArray[] = "'" . $prodi['nama_prodi'] . "'";
                }
                echo implode(",", $prodiArray);
            ?>],
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: false
            }
        });

        $('#addProdiButton').on('click', function() {
            tagifyProdi.addEmptyTag();
        });

        $('#editAddProdiButton').on('click', function() {
            tagifyEditProdi.addEmptyTag();
        });

        $('#modalEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var posisi = button.data('posisi');
            var kualifikasi = button.data('kualifikasi');
            var prodi_id = button.data('prodi_id').split(',');
            var keahlian = button.data('keahlian');

            var modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_nama_pekerjaan').val(nama);
            modal.find('#edit_posisi').val(posisi);
            modal.find('#edit_kualifikasi').val(kualifikasi);
            
            tagifyEditProdi.removeAllTags();
            prodi_id.forEach(function(prodi) {
                tagifyEditProdi.addTags(prodi);
            });

            tagifyEditKeahlian.removeAllTags();
            tagifyEditKeahlian.addTags(keahlian.split(","));
        });

        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('#hapus_id').val(id);
        });

        $('#modalPelamar').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            
            $.ajax({
                url: 'fetch_applicants.php',
                method: 'GET',
                data: { lowongan_id: id },
                success: function(response) {
                    $('#pelamarTableBody').html(response);
                }
            });
        });
    });
</script>



    
</body>
</html>

