<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

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

$search = isset($_POST['search']) ? $_POST['search'] : '';
$jobs = fetchJobs($pdo, $search);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan Kerja</title>
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
        height: 100%;
        /* Ensure each card takes full height */
        display: flex;
        flex-direction: column;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .search-bar {
        margin-bottom: 20px;
    }

    .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .card-text {
        flex-grow: 1;
    }

    .read-more {
        margin-top: auto;
    }

    .read-more-content {
        display: none;
    }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include 'navbar_admin.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Daftar Lowongan Kerja</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Lowongan Kerja</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row search-bar">
                        <div class="col-md-12">
                            <form method="POST" action="perusahaan_admin.php">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search"
                                        value="<?php echo htmlspecialchars($search); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit"><i
                                                class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <?php if ($jobs): ?>
                        <?php foreach ($jobs as $row): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['nama_pekerjaan']); ?></h5>
                                    <p class="card-text"><strong>Posisi:</strong>
                                        <?php echo htmlspecialchars($row['posisi']); ?></p>
                                    <p class="card-text"><strong>Kualifikasi:</strong>
                                        <?php echo htmlspecialchars($row['kualifikasi']); ?></p>
                                    <p class="card-text"><strong>Prodi:</strong>
                                        <?php
                                            $prodi = json_decode($row['prodi'], true);
                                            if (is_array($prodi)) {
                                                $prodi_list = array_map(function($item) {
                                                    return $item['value'];
                                                }, $prodi);
                                                echo htmlspecialchars(implode(', ', $prodi_list));
                                            } else {
                                                echo htmlspecialchars($row['prodi']);
                                            }
                                            ?>
                                            </p>
                                            <p class="card-text"><strong>Keahlian:</strong>
                                        <?php
                                            $keahlian = json_decode($row['keahlian'], true);
                                            if (is_array($keahlian)) {
                                                $keahlian_list = array_map(function($item) {
                                                    return $item['value'];
                                                }, $keahlian);
                                                echo htmlspecialchars(implode(', ', $keahlian_list));
                                            } else {
                                                echo htmlspecialchars($row['keahlian']);
                                            }
                                            ?>
                                    </p>
                                    <p class="card-text"><strong>Tanggal Posting:</strong>
                                        <?php echo htmlspecialchars($row['tanggal_posting']); ?></p>
                                    <p class="card-text"><strong>Batas Waktu:</strong>
                                        <?php echo htmlspecialchars($row['batas_waktu']); ?></p>
                                    <button class='btn btn-primary btn-sm read-more' onclick="toggleReadMore(this)">Selengkapnya</button>
                                    <button class='btn btn-secondary btn-sm' onclick="fetchCompanyDetails(<?php echo $row['perusahaan_id']; ?>)">Detail
                                        Perusahaan</button>
                                    <div class="read-more-content">
                                        <p class="card-text"><strong>Kualifikasi Lengkap:</strong>
                                            <?php echo htmlspecialchars($row['kualifikasi']); ?></p>
                                        </div>
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

        <!-- Modal for company details -->
        <div class="modal fade" id="modalDetailPerusahaan" tabindex="-1" role="dialog"
            aria-labelledby="modalDetailPerusahaanLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetailPerusahaanLabel">Detail Perusahaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Company details will be populated here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
    function fetchCompanyDetails(perusahaanId) {
        $.ajax({
            url: 'get_perusahaan_detail.php',
            method: 'GET',
            data: {
                user_id: perusahaanId
            },
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                } else {
                    var modalBody = `
                        <p><strong>Nama Perusahaan:</strong> ${data.nama_perusahaan}</p>
                        <p><strong>Jenis Perusahaan:</strong> ${data.nama_jenis}</p>
                        <p><strong>Alamat:</strong> ${data.alamat_perusahaan}</p>
                        <p><strong>Email:</strong> ${data.email_perusahaan}</p>
                        <p><strong>Tahun Didirikan:</strong> ${data.tahun_didirikan}</p>
                        <p><strong>Pimpinan Perusahaan:</strong> ${data.pimpinan_perusahaan}</p>
                        <p><strong>Deskripsi:</strong> ${data.deskripsi_perusahaan}</p>
                        <p><strong>No Telp:</strong> ${data.no_telp}</p>
                        <p><strong>Profile Image:</strong><br><img src="${data.profile_image}" alt="Profile Image" style="max-width:100%;height:auto;"></p>
                    `;
                    $('#modalDetailPerusahaan .modal-body').html(modalBody);
                    $('#modalDetailPerusahaan').modal('show');
                }
            },
            error: function() {
                alert('Gagal mengambil detail perusahaan');
            }
        });
    }

    function toggleReadMore(button) {
        var content = button.nextElementSibling;
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
            button.innerText = 'Lebih sedikit';
        } else {
            content.style.display = 'none';
            button.innerText = 'Selengkapnya';
        }
    }
    </script>
</body>

</html>