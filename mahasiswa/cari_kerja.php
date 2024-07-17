<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
include '../includes/db.php';

function fetchMahasiswaDetails($pdo, $user_id) {
    try {
        $sql = "SELECT prodi_id, keahlian FROM mahasiswas WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

function fetchCompanyDetails($pdo, $user_id) {
    try {
        $sql = "SELECT * FROM perusahaans WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

function fetchJobs($pdo, $search = '') {
    try {
        $sql = "SELECT * FROM lowongan_kerja";
        if (!empty($search)) {
            $search = '%' . $search . '%';
            $sql .= " WHERE nama_pekerjaan LIKE :search OR posisi LIKE :search OR kualifikasi LIKE :search";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['search' => $search]);
        } else {
            $stmt = $pdo->query($sql);
        }
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

function fetchFilteredJobs($pdo, $prodi_id, $keahlian, $search = '') {
    try {
        $keahlian = '%' . $keahlian . '%';
        $sql = "SELECT * FROM lowongan_kerja 
                WHERE (JSON_CONTAINS(prodi, JSON_OBJECT('id', :prodi_id)) 
                OR keahlian LIKE :keahlian)";
        if (!empty($search)) {
            $search = '%' . $search . '%';
            $sql .= " AND (nama_pekerjaan LIKE :search OR posisi LIKE :search OR kualifikasi LIKE :search)";
        }
        $stmt = $pdo->prepare($sql);
        $params = ['prodi_id' => $prodi_id, 'keahlian' => $keahlian];
        if (!empty($search)) {
            $params['search'] = $search;
        }
        $stmt->execute($params);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

$search = isset($_POST['search']) ? $_POST['search'] : '';
$mahasiswa = fetchMahasiswaDetails($pdo, $user_id);
if ($mahasiswa) {
    $prodi_id = $mahasiswa['prodi_id'];
    $keahlian = $mahasiswa['keahlian'];
    $jobs = fetchFilteredJobs($pdo, $prodi_id, $keahlian, $search);
} else {
    $jobs = fetchJobs($pdo, $search);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kerja</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .content-wrapper {
            padding: 20px !important;
        }
        .content-header {
            padding: 20px 0 !important;
        }
        .card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .search-bar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'navbar_mhs.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cari Lowongan yang Sesuai</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Cari Kerja</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="row search-bar">
                    <div class="col-md-12">
                        <form method="POST" action="cari_kerja.php">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <?php if ($jobs): ?>
                        <?php foreach ($jobs as $row): ?>
                            <?php
                            // Cek apakah batas waktu sudah terlewati
                            $batas_waktu = new DateTime($row['batas_waktu']);
                            $current_date = new DateTime();
                            $is_expired = $current_date > $batas_waktu;
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($row['nama_pekerjaan']); ?></h5>
                                        <p class="card-text"><strong>Posisi:</strong> <?php echo htmlspecialchars($row['posisi']); ?></p>
                                        <p class="card-text"><strong>Kualifikasi:</strong> <?php echo htmlspecialchars($row['kualifikasi']); ?></p>
                                        <p class="card-text"><strong>Tanggal Posting:</strong> <?php echo htmlspecialchars($row['tanggal_posting']); ?></p>
                                        <p class="card-text"><strong>Batas Waktu:</strong> <?php echo htmlspecialchars($row['batas_waktu']); ?></p>
                                        <button class='btn btn-<?php echo $is_expired ? 'secondary' : 'success'; ?> btn-sm' data-toggle='modal' data-target='#modalLamar' 
                                            data-id="<?php echo $row['id']; ?>"
                                            data-mahasiswa="<?php echo $user_id; ?>"
                                            <?php echo $is_expired ? 'disabled' : ''; ?>>Lamar</button>
                                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalDetailPerusahaan' data-perusahaan-id="<?php echo $row['perusahaan_id']; ?>">Detail Perusahaan</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Tidak ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modalLamar" tabindex="-1" aria-labelledby="modalLamarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLamarLabel">Lamar Pekerjaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formLamar" method="POST" action="lamar_kerja.php">
                    <input type="hidden" name="lowongan_id" id="lowongan_id">
                    <input type="hidden" name="mahasiswa_id" id="mahasiswa_id">
                    <div class="form-group">
                        <label for="pesan">Pesan</label>
                        <textarea class="form-control" name="pesan" id="pesan" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Lamaran</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailPerusahaan" tabindex="-1" aria-labelledby="modalDetailPerusahaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailPerusahaanLabel">Detail Perusahaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nama Perusahaan:</strong> <span id="detailNamaPerusahaan"></span></p>
                <p><strong>Alamat Perusahaan:</strong> <span id="detailAlamatPerusahaan"></span></p>
                <p><strong>Email Perusahaan:</strong> <span id="detailEmailPerusahaan"></span></p>
                <p><strong>Jenis Perusahaan:</strong> <span id="detailJenisPerusahaan"></span></p>
                <p><strong>Tahun Didirikan:</strong> <span id="detailTahunDidirikan"></span></p>
                <p><strong>Pimpinan Perusahaan:</strong> <span id="detailPimpinanPerusahaan"></span></p>
                <p><strong>Deskripsi Perusahaan:</strong> <span id="detailDeskripsiPerusahaan"></span></p>
                <p><strong>No Telp:</strong> <span id="detailNoTelp"></span></p>
            </div>
        </div>
    </div>
</div>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script>
    $('#modalLamar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var mahasiswa = button.data('mahasiswa');

        var modal = $(this);
        modal.find('#lowongan_id').val(id);
        modal.find('#mahasiswa_id').val(mahasiswa);
    });

    $('#modalDetailPerusahaan').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var perusahaanId = button.data('perusahaan-id');
        console.log("Perusahaan ID: " + perusahaanId); // Debugging log

        var modal = $(this);

        $.ajax({
            url: 'get_perusahaan_detail.php',
            method: 'GET',
            data: { user_id: perusahaanId },
            success: function(response) {
                var details = JSON.parse(response);
                console.log(details); // Debugging log
                if (details.error) {
                    modal.find('#detailNamaPerusahaan').text("Data tidak ditemukan");
                    modal.find('#detailAlamatPerusahaan').text("");
                    modal.find('#detailEmailPerusahaan').text("");
                    modal.find('#detailJenisPerusahaan').text("");
                    modal.find('#detailTahunDidirikan').text("");
                    modal.find('#detailPimpinanPerusahaan').text("");
                    modal.find('#detailDeskripsiPerusahaan').text("");
                    modal.find('#detailNoTelp').text("");
                } else {
                    modal.find('#detailNamaPerusahaan').text(details.nama_perusahaan);
                    modal.find('#detailAlamatPerusahaan').text(details.alamat_perusahaan);
                    modal.find('#detailEmailPerusahaan').text(details.email_perusahaan);
                    modal.find('#detailJenisPerusahaan').text(details.jenis_perusahaan_id);
                    modal.find('#detailTahunDidirikan').text(details.tahun_didirikan);
                    modal.find('#detailPimpinanPerusahaan').text(details.pimpinan_perusahaan);
                    modal.find('#detailDeskripsiPerusahaan').text(details.deskripsi_perusahaan);
                    modal.find('#detailNoTelp').text(details.no_telp);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error); // Debugging log
            }
        });
    });
</script>
</body>
</html>
