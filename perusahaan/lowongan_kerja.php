<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear notification after displaying
}
// Fungsi untuk mengambil semua lowongan pekerjaan dari database
function fetchJobs($pdo) {
    try {
        $sql = "SELECT * FROM lowongan_kerja";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching jobs: " . $e->getMessage();
        return false;
    }
}

// Mengambil semua lowongan pekerjaan
$jobs = fetchJobs($pdo);

?>
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
            top: 0; z-index: 1; 
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
        .tag-input-wrapper { 
            position: relative; 
            width: 100%; 
        }
        .tag-input {
            min-height: 38px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            border: 1px solid #ced4da;
            padding: 6px;
            border-radius: 0.25rem; 
            background-color: #fff; 
            overflow: hidden; }
        .tagify__tag {
            margin: 2px; 
        }
        textarea {
            min-height: 80px; 
            width: 100%;
        }

    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'navbar_perusahaan.php'; ?>
    
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
                                        <th>Batas Waktu</th>
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

                                            // Decode JSON prodi dan keahlian
                                            $prodi = json_decode($row['prodi'], true);
                                            $keahlian = json_decode($row['keahlian'], true);

                                            if (is_array($prodi)) {
                                                $prodi_list = array_map(function($item) {
                                                    return $item['value'];
                                                }, $prodi);
                                                echo "<td>" . htmlspecialchars(implode(', ', $prodi_list)) . "</td>";
                                            } else {
                                                echo "<td>" . htmlspecialchars($row['prodi']) . "</td>";
                                            }

                                            if (is_array($keahlian)) {
                                                $keahlian_list = array_map(function($item) {
                                                    return $item['value'];
                                                }, $keahlian);
                                                echo "<td>" . htmlspecialchars(implode(', ', $keahlian_list)) . "</td>";
                                            } else {
                                                echo "<td>" . htmlspecialchars($row['keahlian']) . "</td>";
                                            }
                                            echo "<td>" . htmlspecialchars($row['batas_waktu']) . "</td>"; 
                                            echo "<td>" . htmlspecialchars($row['tanggal_posting']) . "</td>";
                                            echo "<td>
                                                    <button class='btn btn-sm btn-warning editBtn' data-toggle='modal' data-target='#modalEdit' 
                                                        data-id='" . $row['id'] . "' 
                                                        data-nama='" . htmlspecialchars($row['nama_pekerjaan']) . "' 
                                                        data-posisi='" . htmlspecialchars($row['posisi']) . "'
                                                        data-kualifikasi='" . htmlspecialchars($row['kualifikasi']) . "'>Edit</button>
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
    </div>

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
                            <textarea class="form-control" id="kualifikasi" name="kualifikasi" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="prodi">Prodi</label>
                            <input type="text" class="form-control" id="prodi" name="prodi" required>
                        </div>
                        <div class="form-group">
                            <label for="keahlian">Keahlian</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="keahlian" name="keahlian" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="addTagButton">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="batas_waktu">Batas Waktu</label>
                        <input type="date" class="form-control" id="batas_waktu" name="batas_waktu" required>
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
                            <textarea class="form-control" id="edit_kualifikasi" name="edit_kualifikasi" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_prodi">Prodi</label>
                            <input type="text" class="form-control" id="edit_prodi" name="edit_prodi" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_keahlian">Keahlian</label>
                            <input type="text" class="form-control" id="edit_keahlian" name="edit_keahlian" required>
                        </div>
                        <div class="form-group">
                        <label for="edit_batas_waktu">Batas Waktu</label>
                        <input type="date" class="form-control" id="edit_batas_waktu" name="edit_batas_waktu" required>
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

<!-- Modal Portofolio -->
<div class="modal fade" id="modalPortofolio" tabindex="-1" aria-labelledby="modalPortofolioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPortofolioLabel">Portofolio Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="portofolioContent"></div>
            </div>
        </div>
    </div>
</div>

</div>

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
    var tagifyKeahlian = new Tagify(keahlianInput, {
        dropdown: {
            maxItems: 20,
            classname: "tags-look",
            enabled: 0,
            closeOnSelect: false
        }
    });

    $('#addTagButton').on('click', function() {
        tagifyKeahlian.addEmptyTag();
    });

    var editKeahlianInput = document.querySelector('#edit_keahlian');
    var tagifyEditKeahlian = new Tagify(editKeahlianInput, {
        dropdown: {
            maxItems: 20,
            classname: "tags-look",
            enabled: 0,
            closeOnSelect: false
        }
    });

    $('#editAddTagButton').on('click', function() {
        tagifyEditKeahlian.addEmptyTag();
    });

    var prodiInput = document.querySelector('#prodi');
    var tagifyProdi = new Tagify(prodiInput, {
        whitelist: [
            <?php
                $sql_prodi = "SELECT id, nama_prodi FROM prodis";
                $stmt_prodi = $pdo->query($sql_prodi);
                $prodiArray = [];
                while ($prodi = $stmt_prodi->fetch(PDO::FETCH_ASSOC)) {
                    $prodiArray[] = "{id: '" . $prodi['id'] . "', value: '" . $prodi['nama_prodi'] . "'}";
                }
                echo implode(",", $prodiArray);
            ?>
        ],
        dropdown: {
            maxItems: 20,
            classname: "tags-look",
            enabled: 0,
            closeOnSelect: false
        }
    });

    var editProdiInput = document.querySelector('#edit_prodi');
    var tagifyEditProdi = new Tagify(editProdiInput, {
        whitelist: [
            <?php
                $sql_prodi = "SELECT id, nama_prodi FROM prodis";
                $stmt_prodi = $pdo->query($sql_prodi);
                $prodiArray = [];
                while ($prodi = $stmt_prodi->fetch(PDO::FETCH_ASSOC)) {
                    $prodiArray[] = "{id: '" . $prodi['id'] . "', value: '" . $prodi['nama_prodi'] . "'}";
                }
                echo implode(",", $prodiArray);
            ?>
        ],
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

        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_nama_pekerjaan').val(nama);
        modal.find('#edit_posisi').val(posisi);
        modal.find('#edit_kualifikasi').val(kualifikasi);
        modal.find('#edit_batas_waktu').val(batas_waktu); 
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

    $('#modalPortofolio').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var mahasiswaId = button.data('mahasiswa-id');
        var modal = $(this);

        $.ajax({
            url: 'get_portofolio.php',
            method: 'GET',
            data: { mahasiswa_id: mahasiswaId },
            success: function(response) {
                var details = JSON.parse(response);
                var content = '';

                if (details.error) {
                    content = '<p>Data portofolio tidak ditemukan</p>';
                } else {
                    content = '<h5>Sertifikat</h5><ul>';
                    details.sertifikat.forEach(function(item) {
                        content += '<li>' + item.nama_sertifikat + '</li>';
                    });
                    content += '</ul>';

                    content += '<h5>Lomba</h5><ul>';
                    details.lomba.forEach(function(item) {
                        content += '<li>' + item.nama_lomba + '</li>';
                    });
                    content += '</ul>';

                    content += '<h5>Pelatihan</h5><ul>';
                    details.pelatihan.forEach(function(item) {
                        content += '<li>' + item.nama_pelatihan + '</li>';
                    });
                    content += '</ul>';

                    content += '<h5>Proyek</h5><ul>';
                    details.proyek.forEach(function(item) {
                        content += '<li>' + item.nama_proyek + '</li>';
                    });
                    content += '</ul>';
                }

                modal.find('#portofolioContent').html(content);
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                modal.find('#portofolioContent').html('<p>Error loading portofolio</p>');
            }
        });
    });
});
</script>

</body>
</html>