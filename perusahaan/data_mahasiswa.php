<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

// Fungsi untuk mengambil semua data lamaran mahasiswa beserta data mahasiswa
function fetchLamaranMahasiswas($pdo) {
    try {
        $sql = "
            SELECT lm.id AS lamaran_id, lm.lowongan_id, lm.pesan, lm.status AS status_lamaran, m.id AS mahasiswa_id, m.nama_mahasiswa, m.nim, m.jurusan_id, m.jk, m.alamat, m.no_telp, m.tahun_masuk, m.status AS status_mahasiswa, m.email, m.keahlian, lm.salary, l.nama_pekerjaan
            FROM lamaran_mahasiswas lm
            JOIN mahasiswas m ON lm.mahasiswa_id = m.id
            JOIN lowongan_kerja l ON lm.lowongan_id = l.id
        ";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching lamaran mahasiswas: " . $e->getMessage();
        return false;
    }
}

// Mengambil semua data lamaran mahasiswa beserta data mahasiswa
$lamaranMahasiswas = fetchLamaranMahasiswas($pdo);

// Memproses form input salary
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lamaran_id'], $_POST['salary'])) {
    $lamaran_id = $_POST['lamaran_id'];
    $salary = $_POST['salary'];

    try {
        $sql = "UPDATE lamaran_mahasiswas SET salary = :salary WHERE id = :lamaran_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['salary' => $salary, 'lamaran_id' => $lamaran_id]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        echo "Error updating salary: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lamaran Mahasiswa</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
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
        .status-label {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            display: inline-block;
        }
        .status-terima {
            background-color: #ffc107;
        }
        .status-ditolak {
            background-color: #dc3545;
        }
        .status-pending {
            background-color:  #28a745;
            color: black;
        }
        .modal-content .form-group {
            margin-bottom: 1rem;
        }
        .modal-content .form-control {
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'navbar_perusahaan.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <!-- Success and error messages -->
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Daftar Lamaran Mahasiswa</h3>
                        <div class="card-tools">
                            <select id="statusFilter" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="Diterima">Diterima</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="mahasiswaTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>NIM</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No Telepon</th>
                                        <th>Email</th>
                                        <th>Keahlian</th>
                                        <th>Nama Lowongan</th>
                                        <th>Status Lamaran</th>
                                        <th>Pesan</th>
                                        <th>Salary</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($lamaranMahasiswas) {
                                        $no = 1;
                                        foreach ($lamaranMahasiswas as $row) {
                                            $statusClass = strtolower($row['status_lamaran']) == 'diterima' ? 'status-terima' : (strtolower($row['status_lamaran']) == 'ditolak' ? 'status-ditolak' : 'status-pending');
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_mahasiswa']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['jk']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['no_telp']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['keahlian']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_pekerjaan']) . "</td>";
                                            echo "<td><span class='status-label {$statusClass}'>" . htmlspecialchars($row['status_lamaran']) . "</span></td>";
                                            echo "<td>" . htmlspecialchars($row['pesan']) . "</td>";
                                            echo "<td>";
                                            if (isset($row['salary']) && $row['salary'] !== null) {
                                                echo "Rp. " . number_format($row['salary'], 2, ',', '.');
                                            } else {
                                                echo "<button class='btn btn-primary btn-sm' onclick='showSalaryModal(" . $row['lamaran_id'] . ")'>Masukkan Salary</button>";
                                            }
                                            echo "</td>";
                                            echo "<td><button class='btn btn-info btn-sm' onclick=\"window.location.href='lihat_pelamar.php?lowongan_id=" . $row['lowongan_id'] . "&mahasiswa_id=" . $row['mahasiswa_id'] . "'\">Lihat Portofolio</button></td>";
                                            echo "</tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='12'>Tidak ada data</td></tr>";
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
</div>

<!-- Salary Modal -->
<div class="modal fade" id="salaryModal" tabindex="-1" role="dialog" aria-labelledby="salaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="salaryModalLabel">Masukkan Salary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lamaran_id" id="lamaran_id">
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" class="form-control" name="salary" id="salary" required>
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
    function showSalaryModal(lamaran_id) {
        $('#lamaran_id').val(lamaran_id);
        $('#salaryModal').modal('show');
    }

    $(document).ready(function() {
        var table = $('#mahasiswaTable').DataTable();

        $('#statusFilter').on('change', function() {
            var status = $(this).val();
            if (status) {
                table.column(8).search(status).draw();
            } else {
                table.column(8).search('').draw();
            }
        });
    });
</script>
</body>
</html>
