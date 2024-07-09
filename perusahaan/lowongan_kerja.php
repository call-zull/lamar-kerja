<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Function to fetch all jobs from database
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

// Fetching jobs
$jobs = fetchJobs($pdo);

// Process Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $nama_pekerjaan = $_POST['edit_nama_pekerjaan'];
    $posisi = $_POST['edit_posisi'];
    $kualifikasi = $_POST['edit_kualifikasi'];

    try {
        $stmt = $pdo->prepare("UPDATE lowongan_kerja SET nama_pekerjaan = ?, posisi = ?, kualifikasi = ? WHERE id = ?");
        $stmt->execute([$nama_pekerjaan, $posisi, $kualifikasi, $id]);
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error updating job: " . $e->getMessage();
    }
}

// Process Delete Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = $_POST['hapus_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM lowongan_kerja WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error deleting job: " . $e->getMessage();
    }
}

// Process Add Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_pekerjaan'])) {
    $nama_pekerjaan = $_POST['nama_pekerjaan'];
    $posisi = $_POST['posisi'];
    $kualifikasi = $_POST['kualifikasi'];
    $tanggal_posting = date('Y-m-d H:i:s'); // Menggunakan waktu saat ini

    try {
        $stmt = $pdo->prepare("INSERT INTO lowongan_kerja (nama_pekerjaan, posisi, kualifikasi, tanggal_posting) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_pekerjaan, $posisi, $kualifikasi, $tanggal_posting]);
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error adding job: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perusahaan Dashboard</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .content-wrapper {
            margin-top: 20px;
        }
        .table-container {
            position: relative;
        }
        .search-and-add {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-left {
            text-align: left;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <?php include 'navbar_perusahaan.php'; ?>
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <h2 class="header-left">Lowongan Pekerjaan</h2>
                <div class="search-and-add">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search" placeholder="Cari pekerjaan...">
                    </div>
                    <button class="btn btn-primary add-button" data-toggle="modal" data-target="#modalTambah">Tambah</button>
                </div>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Pekerjaan</th>
                                <th>Posisi</th>
                                <th>Kualifikasi</th>
                                <th>Tanggal Posting</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Panggil fungsi fetchJobs() dengan parameter $pdo yang sudah diinisialisasi sebelumnya
                            $jobs = fetchJobs($pdo);
                            
                            // Check apakah ada data yang diambil
                            if ($jobs) {
                                $no = 1;
                                foreach ($jobs as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $no . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_pekerjaan']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['posisi']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['kualifikasi']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tanggal_posting']) . "</td>";
                                    echo "<td>
                                            <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalEdit' 
                                                data-id='" . $row['id'] . "' 
                                                data-nama='" . htmlspecialchars($row['nama_pekerjaan']) . "' 
                                                data-posisi='" . htmlspecialchars($row['posisi']) . "'
                                                data-kualifikasi='" . htmlspecialchars($row['kualifikasi']) . "'>Edit</button>
                                            <button class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalHapus' data-id='" . $row['id'] . "'>Hapus</button>
                                          </td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6'>Tidak ada data</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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

</div>
<!-- ./wrapper -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../app/plugins/fontawesome-free/js/all.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('#dark-mode-toggle').on('click', function() {
            $('body').toggleClass('dark-mode');
        });

        $('#light-mode-toggle').on('click', function() {
            $('body').toggleClass('light-mode');
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
        });

        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('#hapus_id').val(id);
        });
    });
</script>
</body>
</html>
