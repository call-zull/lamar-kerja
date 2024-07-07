<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Function to fetch applicants from lamaran_mahasiswas table
function fetchApplicants($pdo) {
    try {
        $sql = "SELECT lm.*, u.username as nama_mahasiswa, lk.nama_pekerjaan as nama_pekerjaan, lk.posisi
        FROM lamaran_mahasiswas lm
        JOIN users u ON lm.user_id = u.id
        JOIN lowongan_kerja lk ON lm.lowongan_id = lk.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']);
}


// Fetching applicants
$applicants = fetchApplicants($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perusahaan Dashboard - Pelamar</title>
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
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">SI Portofolio</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" id="dark-mode-toggle" role="button">
                    <i class="fas fa-moon"></i>
                    Dark Mode
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="light-mode-toggle" role="button">
                    <i class="fas fa-sun"></i>
                    Light Mode
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <img src="../app/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">SI Portofolio</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <!-- Profile -->
                    <li class="nav-item">
                        <a href="profile_perusahaan.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Profile</p>
                        </a>
                    </li>
                    <!-- Lowongan Kerja -->
                    <li class="nav-item">
                        <a href="../perusahaan/lowongan_kerja.php" class="nav-link">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>Lowongan Kerja</p>
                        </a>
                    </li>
                    <!-- Pelamar -->
                    <li class="nav-item">
                        <a href="../perusahaan/pelamar.php" class="nav-link active">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Pelamar</p>
                        </a>
                    </li>
                    <!-- Logout -->
                    <li class="nav-item">
                        <a href="../auth/logout.php" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
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
                <h2 class="header-left">Pelamar</h2>
                <div class="search-and-add">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search" placeholder="Cari pelamar...">
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Pelamar</th>
                                <th>Nama Pekerjaan</th>
                                <th>Posisi</th>
                                <th>Pesan Pelamar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($applicants) {
                                $no = 1;
                                foreach ($applicants as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $no . "</td>";
                                                echo "<td>" . htmlspecialchars($row['nama_mahasiswa'] ?? '') . "</td>";
                                                echo "<td>" . htmlspecialchars($row['nama_pekerjaan'] ?? '') . "</td>";
                                                echo "<td>" . htmlspecialchars($row['posisi'] ?? '') . "</td>";
                                                echo "<td>" . htmlspecialchars($row['pesan'] ?? '') . "</td>";
                                                echo "<td>" . htmlspecialchars($row['status'] ?? '') . "</td>";
                                                echo "<td>
                                                        <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalEdit' 
                                                            data-id='" . $row['id'] . "' 
                                                            data-status='" . htmlspecialchars($row['status'] ?? '') . "'>Ubah status lamaran </button>
                                                        <button class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalHapus' data-id='" . $row['id'] . "'>Hapus</button>
                                                      </td>";
                                                echo "</tr>";
                                                $no++;
                                            }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada data</td></tr>";
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

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../perusahaan/pelamar_edit.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Pelamar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="edit_id">
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <input type="text" class="form-control" id="edit_status" name="edit_status" required>
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
                <form action="../perusahaan/pelamar_hapus.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHapusLabel">Hapus Pelamar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hapus_id" name="hapus_id">
                        <p>Apakah Anda yakin ingin menghapus pelamar ini?</p>
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
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../app/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../app/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Dark Mode Toggle
    $('#dark-mode-toggle').click(function() {
        $('body').toggleClass('dark-mode');
    });

    // Light Mode Toggle
    $('#light-mode-toggle').click(function() {
        $('body').removeClass('dark-mode');
    });

    // Fill Edit Modal with Data
    $('#modalEdit').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var status = button.data('status');
        
        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_nama_pelamar').val(nama);
        modal.find('#edit_status').val(status);
    });

    // Fill Hapus Modal with Data
    $('#modalHapus').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        
        var modal = $(this);
        modal.find('#hapus_id').val(id);
    });

    // Search Functionality
    $('#search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
</body>
</html>
