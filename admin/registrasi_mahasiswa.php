<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fetch existing mahasiswa data
$stmt_mahasiswa = $pdo->query("SELECT m.*, u.username, u.no_telp FROM mahasiswas m JOIN users u ON m.user_id = u.id WHERE m.approved = 0");

// Current date for display
$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];

$day = $days[date('l')];
$date = date('d');
$month = $months[date('F')];
$year = date('Y');
$currentDate = "$day, $date $month $year";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa - Admin</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Include Navbar and Sidebar for admin -->
    <?php include 'navbar_admin.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3 class="m-0">Registrasi Mahasiswa</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">User</a></li>
                            <li class="breadcrumb-item active">Registrasi Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Mahasiswa</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>NIM</th>
                                        <th>Jurusan</th>
                                        <th>Prodi</th>
                                        <th>No. WhatsApp</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $number = 1; // Variable untuk nomor urut
                                    while ($mahasiswa = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr>';
                                        echo '<td>' . $number . '</td>'; // Menampilkan nomor urut
                                        echo '<td>' . (isset($mahasiswa['username']) ? htmlspecialchars($mahasiswa['username']) : '') . '</td>';
                                        echo '<td>' . (isset($mahasiswa['nim']) ? htmlspecialchars($mahasiswa['nim']) : '') . '</td>';
                                        
                                        // Fetch jurusan name based on jurusan_id
                                        $stmt_jurusan_name = $pdo->prepare("SELECT nama_jurusan FROM jurusans WHERE id = ?");
                                        $stmt_jurusan_name->execute([$mahasiswa['jurusan_id']]);
                                        $jurusan_name = $stmt_jurusan_name->fetchColumn();
                                        
                                        echo '<td>' . htmlspecialchars($jurusan_name) . '</td>';
                                        
                                        // Fetch prodi name based on prodi_id
                                        $stmt_prodi_name = $pdo->prepare("SELECT nama_prodi FROM prodis WHERE id = ?");
                                        $stmt_prodi_name->execute([$mahasiswa['prodi_id']]);
                                        $prodi_name = $stmt_prodi_name->fetchColumn();
                                        
                                        echo '<td>' . htmlspecialchars($prodi_name) . '</td>';
                                        echo '<td>' . (isset($mahasiswa['no_telp']) ? htmlspecialchars($mahasiswa['no_telp']) : '') . '</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-success btn-sm btn-terima" data-mahasiswa-id="' . $mahasiswa['user_id'] . '" data-username="' . htmlspecialchars($mahasiswa['username']) . '" data-whatsapp="' . htmlspecialchars($mahasiswa['no_telp']) . '">Terima</button> ';
                                        echo '<button class="btn btn-danger btn-sm btn-tolak" data-mahasiswa-id="' . $mahasiswa['user_id'] . '" data-username="' . htmlspecialchars($mahasiswa['username']) . '">Tolak</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                        $number++; // Increment nomor urut
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

<!-- Bootstrap modal -->
<div class="modal fade" id="konfirmasiModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Aksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Placeholder untuk pesan konfirmasi -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiAksi">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Include external JS file -->
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle click on "Terima" button
        $('.btn-terima').click(function() {
            var mahasiswaId = $(this).data('mahasiswa-id');
            var username = $(this).data('username');
            var whatsappNumber = $(this).data('whatsapp');
            $('#konfirmasiModal').find('.modal-body').html('Apakah Anda yakin ingin menerima mahasiswa ' + username + '?');
            $('#btnKonfirmasiAksi').removeClass('btn-danger').addClass('btn-success').attr('data-mahasiswa-id', mahasiswaId).attr('data-username', username).attr('data-whatsapp', whatsappNumber); // Atur kelas untuk tombol konfirmasi
            $('#konfirmasiModal').modal('show');
        });

        // Handle konfirmasi modal action
        $('#btnKonfirmasiAksi').click(function() {
            var mahasiswaId = $(this).attr('data-mahasiswa-id');
            var username = $(this).attr('data-username');
            var whatsappNumber = $(this).attr('data-whatsapp');
            
            if (mahasiswaId !== null) {
                var actionUrl = '';
                var actionType = '';
                
                if ($(this).hasClass('btn-success')) { // Periksa kelas pada tombol konfirmasi
                    actionUrl = 'aksi_terima_mahasiswa.php';
                    actionType = 'Terima';
                    
                    // Buka aplikasi WhatsApp dan kirim pesan otomatis
                    if (whatsappNumber) {
                        var pesan = 'Halo ' + username + ', akun Anda sudah dikonfirmasi oleh admin. Silakan masuk menggunakan username dan password yang sudah Anda daftarkan sebelumnya.';
                        var whatsappUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(pesan);
                        window.open(whatsappUrl, '_blank');
                    }
                } else if ($(this).hasClass('btn-danger')) { // Periksa kelas pada tombol konfirmasi
                    actionUrl = 'aksi_tolak_mahasiswa.php';
                    actionType = 'Tolak';
                }

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: { mahasiswa_id: mahasiswaId },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            console.log('Mahasiswa ' + actionType + ' berhasil');
                            $('button[data-mahasiswa-id="' + mahasiswaId + '"]').closest('tr').remove(); // Hapus baris dari tabel
                        } else {
                            console.error('Error:', res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            $('#konfirmasiModal').modal('hide');
        });

        // Handle click on "Tolak" button
        $('.btn-tolak').click(function() {
            var mahasiswaId = $(this).data('mahasiswa-id');
            var username = $(this).data('username');
            $('#konfirmasiModal').find('.modal-body').html('Apakah Anda yakin ingin menolak mahasiswa ' + username + '?');
            $('#btnKonfirmasiAksi').removeClass('btn-success').addClass('btn-danger').attr('data-mahasiswa-id', mahasiswaId).attr('data-username', username); // Atur kelas untuk tombol konfirmasi
            $('#konfirmasiModal').modal('show');
        });

    });
</script>
</body>
</html>
