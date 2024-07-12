<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fetch existing perusahaan data
$stmt_perusahaan = $pdo->query("SELECT * FROM users WHERE approved = 0 AND role = 'perusahaan'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Perusahaan - Admin</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Bootstrap modal CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
               .nav-sidebar .nav-link.active {
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

    <!-- Include Navbar and Sidebar for admin -->
    <?php include 'navbar_admin.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3 class="m-0">Registrasi Perusahaan</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">User</a></li>
                            <li class="breadcrumb-item active">Registrasi Perusahaan</li>
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
                                <h3 class="card-title">Daftar Perusahaan</h3>
                                <!-- Add Export to Excel and PDF buttons -->
                                <div class="card-tools">
                                    <a href="export_excel_perusahaan.php" class="btn btn-primary">Export to Excel</a>
                                    <a href="export_pdf_perusahaan.php" class="btn btn-danger">Lihat PDF</a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>No. WhatsApp</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $number = 1; // Variable untuk nomor urut
                                    while ($perusahaan = $stmt_perusahaan->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr>';
                                        echo '<td>' . $number . '</td>'; // Menampilkan nomor urut
                                        echo '<td>' . (isset($perusahaan['username']) ? htmlspecialchars($perusahaan['username']) : '') . '</td>';
                                        echo '<td>' . (isset($perusahaan['no_telp']) ? htmlspecialchars($perusahaan['no_telp']) : '') . '</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-success btn-sm btn-terima" data-perusahaan-id="' . $perusahaan['id'] . '" data-username="' . htmlspecialchars($perusahaan['username']) . '" data-whatsapp="' . htmlspecialchars($perusahaan['no_telp']) . '">Terima</button> ';
                                        echo '<button class="btn btn-danger btn-sm btn-tolak" data-perusahaan-id="' . $perusahaan['id'] . '" data-username="' . htmlspecialchars($perusahaan['username']) . '">Tolak</button>';
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
<script src="script_admin.js"></script>
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script>
$(document).ready(function() {
    // Handle click on "Terima" button
    $('.btn-terima').click(function() {
        var perusahaanId = $(this).data('perusahaan-id');
        var username = $(this).data('username');
        var whatsappNumber = $(this).data('whatsapp');
        $('#konfirmasiModal').find('.modal-body').html('Apakah Anda yakin ingin menerima perusahaan ' + username + '?');
        $('#btnKonfirmasiAksi').removeClass('btn-danger').addClass('btn-success').attr('data-perusahaan-id', perusahaanId).attr('data-username', username).attr('data-whatsapp', whatsappNumber); // Atur kelas untuk tombol konfirmasi
        $('#konfirmasiModal').modal('show');
    });

    // Handle konfirmasi modal action
    $('#btnKonfirmasiAksi').click(function() {
        var perusahaanId = $(this).attr('data-perusahaan-id');
        var username = $(this).attr('data-username');
        var whatsappNumber = $(this).attr('data-whatsapp');
        
        if (perusahaanId !== null) {
            var actionUrl = '';
            var actionType = '';
            
            if ($(this).hasClass('btn-success')) { // Periksa kelas pada tombol konfirmasi
                actionUrl = 'aksi_terima_perusahaan.php';
                actionType = 'Terima';
                
                // Buka aplikasi WhatsApp dan kirim pesan otomatis
                if (whatsappNumber) {
                    var pesan = 'Halo atas nama perusahaan ' + username + ', akun Anda sudah dikonfirmasi oleh admin. Silakan masuk menggunakan username dan password yang sudah Anda daftarkan sebelumnya.';
                    var whatsappUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(pesan);
                    window.open(whatsappUrl, '_blank');
                }
            } else if ($(this).hasClass('btn-danger')) { // Periksa kelas pada tombol konfirmasi
                actionUrl = 'aksi_tolak_perusahaan.php';
                actionType = 'Tolak';
            }

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: { perusahaan_id: perusahaanId },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        console.log('Perusahaan ' + actionType + ' berhasil');
                        $('button[data-perusahaan-id="' + perusahaanId + '"]').closest('tr').remove(); // Hapus baris dari tabel
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
        var perusahaanId = $(this).data('perusahaan-id');
        var username = $(this).data('username');
        $('#konfirmasiModal').find('.modal-body').html('Apakah Anda yakin ingin menolak perusahaan ' + username + '?');
        $('#btnKonfirmasiAksi').removeClass('btn-success').addClass('btn-danger').attr('data-perusahaan-id', perusahaanId).attr('data-username', username); // Atur kelas untuk tombol konfirmasi
        $('#konfirmasiModal').modal('show');
    });
});
</script>
</body>
</html>
