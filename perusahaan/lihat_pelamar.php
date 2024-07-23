<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

if (!isset($_GET['lowongan_id'])) {
    echo "Lowongan ID tidak ditemukan.";
    exit;
}

$lowongan_id = $_GET['lowongan_id'];

// Ambil data lamaran dan status pelamar
$sql = "SELECT mahasiswa_id, status FROM lamaran_mahasiswas WHERE lowongan_id = :lowongan_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['lowongan_id' => $lowongan_id]);
$lamaran = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lamaran) {
    echo "Lamaran tidak ditemukan.";
    exit;
}

$mahasiswa_id = $lamaran['mahasiswa_id'];
$status_lamaran = $lamaran['status'];

// Query untuk mengambil data mahasiswa berdasarkan mahasiswa_id
$sql = "SELECT m.*, p.nama_prodi, j.nama_jurusan 
        FROM mahasiswas m
        JOIN prodis p ON m.prodi_id = p.id
        JOIN jurusans j ON m.jurusan_id = j.id
        WHERE m.id = :mahasiswa_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['mahasiswa_id' => $mahasiswa_id]);

$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa) {
    echo "Mahasiswa tidak ditemukan.";
    exit;
}

// Query untuk mengambil data sertifikasi
$sql_sertifikasi = "SELECT * FROM sertifikasi WHERE mahasiswa_id = :mahasiswa_id";
$stmt_sertifikasi = $pdo->prepare($sql_sertifikasi);
$stmt_sertifikasi->execute(['mahasiswa_id' => $mahasiswa_id]);
$sertifikasi = $stmt_sertifikasi->fetchAll(PDO::FETCH_ASSOC);
// die('$sertifikasi');

// Query untuk mengambil data proyek
$sql_proyek = "SELECT * FROM proyek WHERE mahasiswa_id = :mahasiswa_id";
$stmt_proyek = $pdo->prepare($sql_proyek);
$stmt_proyek->execute(['mahasiswa_id' => $mahasiswa_id]);
$proyek = $stmt_proyek->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data lomba
$sql_lomba = "SELECT * FROM lomba WHERE mahasiswa_id = :mahasiswa_id";
$stmt_lomba = $pdo->prepare($sql_lomba);
$stmt_lomba->execute(['mahasiswa_id' => $mahasiswa_id]);
$lomba = $stmt_lomba->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data pelatihan
$sql_pelatihan = "SELECT * FROM pelatihan WHERE mahasiswa_id = :mahasiswa_id";
$stmt_pelatihan = $pdo->prepare($sql_pelatihan);
$stmt_pelatihan->execute(['mahasiswa_id' => $mahasiswa_id]);
$pelatihan = $stmt_pelatihan->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Pelamar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <style>
    .content-wrapper {
        margin-top: 20px;
        background-color: #f4f6f9;
        padding: 20px;
    }

    .card {
        margin-bottom: 20px;
    }

    .card-body p {
        margin: 0;
        padding: 0;
    }

    .btn-container {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 8px;
    }

    .status-container {
        text-align: center;
        padding: 10px;
        color: #fff;
        border-radius: 5px;
    }

    .status-diterima {
        background-color: green;
    }

    .status-ditolak {
        background-color: red;
    }

    .sidebar-mini .main-sidebar {
        margin-bottom: 0;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include 'navbar_perusahaan.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <!-- <h1 class="m-0">Profile Perusahaan</h1> -->
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="lowongan_kerja.php">Kembali</a></li>
                                <li class="breadcrumb-item active">Lihat Pelamar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Informasi Mahasiswa -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Mahasiswa</h3>
                                    <div class="btn-container">
                                        <?php if ($status_lamaran != 'diterima' && $status_lamaran != 'ditolak'): ?>
                                        <button class="btn btn-success" id="terimaBtn">Terima</button>
                                        <button class="btn btn-danger" id="tolakBtn">Tolak</button>
                                        <button class="btn btn-primary" id="interviewBtn">Jadwalkan Interview</button>
                                        <?php endif; ?>

                                        <?php if ($status_lamaran == 'diterima'): ?>
                                        <div class="status-container status-diterima">Pelamar Diterima</div>
                                        <?php elseif ($status_lamaran == 'ditolak'): ?>
                                        <div class="status-container status-ditolak">Pelamar Ditolak</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if ($mahasiswa): ?>
                                        <div class="row">
                                            <div class="col-md-4 text-center">
                                            <?php
                                                $profileImage = !empty($mahasiswa['profile_image']) ? '../assets/mahasiswa/profile/' . rawurlencode(htmlspecialchars($mahasiswa['profile_image'])) : '../../assets/images/profile_default.png';
                                                ?>
                                                <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="img-fluid rounded-circle" style="max-width: 150px;">
                                            <h3 class="mt-2">
                                                <?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?>
                                            </h3>
                                            </div>
                                            <div class="col-md-8">
                                            <p><strong>NIM:</strong> <?php echo htmlspecialchars($mahasiswa['nim']); ?>
                                            </p>
                                            <p><strong>Jurusan:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['nama_jurusan']); ?></p>
                                            <p><strong>Prodi:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['nama_prodi']); ?></p>
                                            <p><strong>Jenis Kelamin:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['jk']); ?></p>
                                            <p><strong>Tahun Masuk:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['tahun_masuk']); ?></p>
                                            <p><strong>Status:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['status']); ?></p>
                                            <p><strong>Alamat:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['alamat']); ?></p>
                                            <p><strong>Email:</strong>
                                                <?php echo htmlspecialchars($mahasiswa['email']); ?></p>
                                            <p><strong>Keahlian:</strong>
                                            <p>
                                                <?php
                                                    $keahlian = explode(',', $mahasiswa['keahlian']);
                                                    foreach ($keahlian as $skill) {
                                                        echo '<span class="badge badge-primary mr-1">' . htmlspecialchars(trim($skill)) . '</span>';
                                                    }
                                                    ?>
                                            </p>

                                            <?php
                                                    $ipk = [];
                                                    for ($i = 1; $i <= 8; $i++) {
                                                        if (!empty($mahasiswa['ipk_semester_' . $i])) {
                                                            $ipk[] = $mahasiswa['ipk_semester_' . $i];
                                                        }
                                                    }
                                                    if ($ipk) {
                                                        $average_ipk = array_sum($ipk) / count($ipk);
                                                        echo '<p><strong>IPK Rata-rata:</strong> ' . htmlspecialchars(number_format($average_ipk, 2)) . '</p>';
                                                    } else {
                                                        echo '<p>Tidak ada data IPK.</p>';
                                                    }
                                                ?>
                                        </div>
                                        </div>
                                    <?php else: ?>
                                    <p>Mahasiswa tidak ditemukan.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Portofolio Mahasiswa -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Portofolio Mahasiswa</h3>
                                </div>
                                <div class="card-body">
                                    <?php if ($mahasiswa): ?>
                                    <h4>Dokumen</h4>
                                    <p><strong>Ijazah:</strong> <a
                                            href="../assets/mahasiswa/ijazahatautranskip/<?php echo htmlspecialchars($mahasiswa['ijazah']); ?>"
                                            target="_blank">Lihat Ijazah</a></p>
                                    <p><strong>Resume:</strong> <a href="../assets/mahasiswa/resume/<?php echo htmlspecialchars($mahasiswa['resume']); ?>"
                                            target="_blank">Lihat Resume</a></p>
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <?php if (!empty($mahasiswa['khs_semester_' . $i])): ?>
                                                <p><strong>KHS Semester <?php echo $i; ?>:</strong> <a
                                            href="../assets/mahasiswa/khs/<?php echo htmlspecialchars($mahasiswa['khs_semester_' . $i]); ?>"
                                            target="_blank">Lihat KHS</a></p>
                                    <?php endif; ?>
                                    <?php endfor; ?>

                                    <h4>Sertifikasi</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Sertifikasi</th>
                                                    <th>Nomor SK</th>
                                                    <th>Lembaga</th>
                                                    <th>Tanggal Diperoleh</th>
                                                    <th>Tanggal Kadaluwarsa</th>
                                                    <th>Bukti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($sertifikasi): ?>
                                                    <?php foreach ($sertifikasi as $index => $row): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1; ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_sertifikasi']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nomor_sk']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lembaga_id']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tanggal_diperoleh']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tanggal_kadaluarsa']); ?></td>
                                                    <td><a href="../../assets/mahasiswa/sertifikasi/<?php echo htmlspecialchars($row['bukti']); ?>" target="_blank">Lihat
                                                            Bukti</a></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7">Tidak ada data sertifikasi.</td>
                                                        </tr>
                                                <?php endif; ?>
                                                </tbody>
                                                </table>
                                                </div>

                                    <h4>Lomba</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Lomba</th>
                                                    <th>Prestasi</th>
                                                    <th>Kategori</th>
                                                    <th>Tingkatan</th>
                                                    <th>Penyelenggara</th>
                                                    <th>Tanggal Pelaksanaan</th>
                                                    <th>Tempat Pelaksanaan</th>
                                                    <th>Bukti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($lomba): ?>
                                                    <?php foreach ($lomba as $index => $row): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1; ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_lomba']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['prestasi']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['id_kategori']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['id_tingkatan']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['penyelenggara']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['tanggal_pelaksanaan']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['tempat_pelaksanaan']); ?></td>
                                                    <td><a href="../../assets/mahasiswa/lomba/<?php echo htmlspecialchars($row['bukti']); ?>" target="_blank">Lihat
                                                            Bukti</a></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="9">Tidak ada data lomba.</td>
                                                        </tr>
                                                <?php endif; ?>
                                                </tbody>
                                                </table>
                                                </div>

                                    <h4>Pelatihan</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Pelatihan</th>
                                                    <th>Materi</th>
                                                    <th>Deskripsi</th>
                                                    <th>Tingkatan</th>
                                                    <th>Penyelenggara</th>
                                                    <th>Tanggal Mulai</th>
                                                    <th>Tanggal Selesai</th>
                                                    <th>Tempat Pelaksanaan</th>
                                                    <th>Bukti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($pelatihan): ?>
                                                    <?php foreach ($pelatihan as $index => $row): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1; ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_pelatihan']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['materi']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['id_tingkatan']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['penyelenggara']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tanggal_mulai']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tanggal_selesai']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tempat_pelaksanaan']); ?></td>
                                                    <td><a href="../../assets/mahasiswa/pelatihan/<?php echo htmlspecialchars($row['bukti']); ?>" target="_blank">Lihat
                                                            Bukti</a></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="10">Tidak ada data pelatihan.</td>
                                                        </tr>
                                                <?php endif; ?>
                                                </tbody>
                                                </table>
                                                </div>

                                    <h4>Proyek</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Proyek</th>
                                                    <th>Partner</th>
                                                    <th>Peran</th>
                                                    <th>Waktu Awal</th>
                                                    <th>Waktu Selesai</th>
                                                    <th>Tujuan Proyek</th>
                                                    <th>Bukti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($proyek): ?>
                                                    <?php foreach ($proyek as $index => $row): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1; ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_proyek']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['partner']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['peran']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['waktu_awal']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['waktu_selesai']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tujuan_proyek']); ?></td>
                                                    <td><a href="../../assets/mahasiswa/proyek/<?php echo htmlspecialchars($row['bukti']); ?>" target="_blank">Lihat
                                                            Bukti</a></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="8">Tidak ada data proyek.</td>
                                                        </tr>
                                                <?php endif; ?>
                                                </tbody>
                                                </table>
                                                </div>

                                    <?php else: ?>
                                    <p>Mahasiswa tidak ditemukan.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menerima mahasiswa ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Yakin</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk input salary -->
    <div class="modal fade" id="salaryModal" tabindex="-1" role="dialog" aria-labelledby="salaryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salaryModalLabel">Masukkan Salary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="salaryForm">
                        <div class="form-group">
                            <label for="salaryInput">Salary</label>
                            <input type="number" class="form-control" id="salaryInput" required>
                        </div>
                        <input type="hidden" id="idPelamar" value="<?php echo $mahasiswa_id; ?>">
                        <input type="hidden" id="lowonganId" value="<?php echo $lowongan_id; ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="submitSalary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationMessage">
                    <!-- Pesan notifikasi akan ditampilkan di sini -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../app/plugins/fontawesome-free/js/all.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
    $('#terimaBtn').on('click', function() {
        console.log("Terima button clicked");
        $('#confirmModal').modal('show');
    });

    $('#confirmButton').on('click', function() {
        console.log("Confirm button clicked");
        $('#confirmModal').modal('hide');
        $('#salaryModal').modal('show');
    });

    $('#submitSalary').on('click', function() {
        console.log("Submit salary button clicked");
        var salary = $('#salaryInput').val();
        var id_pelamar = $('#idPelamar').val();
        var lowongan_id = $('#lowonganId').val();

        if (salary !== null && salary.trim() !== "") {
            $.ajax({
                url: 'terima_pelamar.php',
                method: 'POST',
                data: {
                    id_pelamar: id_pelamar,
                    salary: salary,
                    lowongan_id: lowongan_id
                },
                success: function(response) {
                    console.log("Response received: " + response);
                    var result = JSON.parse(response);
                    $('#salaryModal').modal('hide');
                    $('#notificationMessage').text(result.message);
                    $('#notificationModal').modal('show');
                    if (result.success) {
                        // Hide buttons and show status message
                        $('.btn-container').html(
                            '<div class="status-container status-diterima">Pelamar Diterima</div>'
                        );
                    }
                }
            });
        } else {
            alert("Salary harus diisi.");
        }
    });

    $('#tolakBtn').on('click', function() {
        console.log("Tolak button clicked");
        $.ajax({
            url: 'tolak_pelamar.php',
            method: 'POST',
            data: {
                id_pelamar: <?php echo $mahasiswa_id; ?>,
                lowongan_id: <?php echo $lowongan_id; ?>
            },
            success: function(response) {
                console.log("Response received: " + response);
                var result = JSON.parse(response);
                $('#notificationMessage').text(result.message);
                $('#notificationModal').modal('show');
                if (result.success) {
                    // Hide buttons and show status message
                    $('.btn-container').html(
                        '<div class="status-container status-ditolak">Pelamar Ditolak</div>');
                }
            }
        });
    });

    $('#interviewBtn').on('click', function() {
        console.log("Interview button clicked");
        var phone_pelamar = "<?php echo $mahasiswa['no_telp']; ?>";
        var message = encodeURIComponent("Halo, kami ingin menjadwalkan wawancara untuk Anda.");
        var whatsappUrl = "https://wa.me/" + phone_pelamar + "?text=" + message;
        window.open(whatsappUrl, '_blank');
    });
    </script>

</body>

</html>