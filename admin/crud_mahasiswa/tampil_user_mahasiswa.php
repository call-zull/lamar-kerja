<?php
session_start();
include '../../includes/db.php';

// Fetch prodis from database
// $sql_prodis = "SELECT id, nama_prodi FROM prodis";
// $stmt_prodis = $pdo->query($sql_prodis);
// $prodis = $stmt_prodis->fetchAll();

// Fetch jurusans from database
$sql_jurusans = "SELECT id, nama_jurusan FROM jurusans";
$stmt_jurusans = $pdo->query($sql_jurusans);
$jurusans = $stmt_jurusans->fetchAll();

// Redirect unauthorized users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

// Check for success message notification
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear notification after displaying
}

// Function to get enum values
function getEnumValues($pdo, $table, $column) {
    $query = "SHOW COLUMNS FROM $table LIKE '$column'";
    $stmt = $pdo->query($query);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $enum = $row['Type'];
    preg_match("/^enum\(\'(.*)\'\)$/", $enum, $matches);
    $enum = explode("','", $matches[1]);
    return $enum;
}

// Get enum values 
$statuses = getEnumValues($pdo, 'mahasiswas', 'status');
$jks = getEnumValues($pdo, 'mahasiswas', 'jk');

// Fetch mahasiswa data from database
$sql_mahasiswas = "SELECT b.id, b.user_id, b.nim, b.nama_mahasiswa, b.profile_image, b.prodi_id, b.jurusan_id, p.nama_prodi, j.nama_jurusan, b.tahun_masuk, b.status, b.jk, b.alamat, b.email, b.no_telp
                   FROM mahasiswas b 
                   LEFT JOIN prodis p ON b.prodi_id = p.id
                   LEFT JOIN jurusans j ON b.jurusan_id = j.id";
$stmt = $pdo->prepare($sql_mahasiswas);
$stmt->execute();
$mahasiswas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil User Mahasiswa</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <!-- AdminLTE styles -->
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- DataTables -->
    <link rel="stylesheet" href="../../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Custom Styles -->
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
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar_admin.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
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
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Tampil User Mahasiswa</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Tampil User Mahasiswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Data Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-users"></i>
                                        Data Mahasiswa
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                                            <i class="fas fa-user-plus"></i> Tambah Akun
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="adminTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Profile</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Jurusan</th>
                                                    <th>Prodi</th>
                                                    <th>Tahun Masuk</th>
                                                    <th>Status</th>
                                                    <th>Jenis Kelamin</th>
                                                    <th>Alamat</th>
                                                    <th>Email</th>
                                                    <th>No.Telp</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($mahasiswas as $index => $mahasiswa) : ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['nim']) ?></td>
                                                        <td>
                                                            <?php if (!empty($mahasiswa['profile_image'])) : ?>
                                                                <img src="../assets/mahasiswa/<?= htmlspecialchars($mahasiswa['profile_image']) ?>" class="img-thumbnail" style="max-width: 50px;">
                                                            <?php else : ?>
                                                                No Image
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($mahasiswa['nama_mahasiswa']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['nama_jurusan']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['nama_prodi']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['tahun_masuk']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['status']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['jk']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['alamat']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['email']) ?></td>
                                                        <td><?= htmlspecialchars($mahasiswa['no_telp']) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary btn-edit" data-id="<?= $mahasiswa['id'] ?>" data-nim="<?= htmlspecialchars($mahasiswa['nim']) ?>" data-nama="<?= htmlspecialchars($mahasiswa['nama_mahasiswa']) ?>" data-jurusan="<?= $mahasiswa['jurusan_id'] ?>" data-prodi="<?= $mahasiswa['prodi_id'] ?>" data-tahun-masuk="<?= htmlspecialchars($mahasiswa['tahun_masuk']) ?>" data-status="<?= htmlspecialchars($mahasiswa['status']) ?>" data-jk="<?= htmlspecialchars($mahasiswa['jk']) ?>" data-alamat="<?= htmlspecialchars($mahasiswa['alamat']) ?>" data-email="<?= htmlspecialchars($mahasiswa['email']) ?>" data-no-telp="<?= htmlspecialchars($mahasiswa['no_telp']) ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $mahasiswa['id'] ?>" data-user="<?php echo $mahasiswa['user_id']; ?>">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div><!-- /.card-body -->
                            </div><!-- /.card -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="addUserForm" action="process_add_mahasiswa.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus"></i> Tambah Akun Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="nim">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim" required>
                            </div>
                            <div class="form-group">
                                <label for="nama_mahasiswa">Nama Mahasiswa</label>
                                <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" required>
                            </div>
                            <div class="form-group">
                                <label for="jurusan">Jurusan</label>
                                <select class="form-control" id="jurusan" name="jurusan">
                                    <option value="">-- Pilih Jurusan --</option>
                                    <?php foreach ($jurusans as $jurusan) : ?>
                                        <option value="<?php echo $jurusan['id']; ?>"><?php echo $jurusan['nama_jurusan']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="prodi">Prodi</label>
                                <select class="form-control" id="prodi" name="prodi">
                                    <option value="">-- Pilih Prodi --</option>
                                    <!-- Pilihan Prodi akan di-load menggunakan JavaScript/Ajax -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tahun_masuk">Tahun Masuk</label>
                                <input type="number" class="form-control" id="tahun_masuk" name="tahun_masuk" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($statuses as $status) : ?>
                                        <option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jk">Jenis Kelamin</label>
                                <select class="form-control" id="jk" name="jk" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <?php foreach ($jks as $jk) : ?>
                                        <option value="<?= htmlspecialchars($jk) ?>"><?= htmlspecialchars($jk) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="harus mengandung karakter @" required id="email">
                            </div>
                            <div class="form-group">
                                <label for="no_telp">No. Telp</label>
                                <input type="tel" class="form-control" id="no_telp" name="no_telp" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Tambah Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

                

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="editMahasiswaForm" method="post" action="update_user_mahasiswa.php"> 
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel"><i class="fas fa-user-edit"></i> Edit Akun Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_mahasiswa_id" name="mahasiswa_id">
                            <div class="form-group">
                                <label for="edit_nim">NIM</label>
                                <input type="text" class="form-control" id="edit_nim" name="nim" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_nama_mahasiswa">Nama Mahasiswa</label>
                                <input type="text" class="form-control" id="edit_nama_mahasiswa" name="nama_mahasiswa" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_jurusan">Jurusan</label>
                                <select class="form-control" id="edit_jurusan" name="jurusan">
                                    <option value="">-- Pilih Jurusan --</option>
                                    <?php foreach ($jurusans as $jurusan) : ?>
                                        <?php $selected_jurusan = ($jurusan['id'] == $admin['jurusan_id']) ? 'selected' : ''; ?>
                                        <option value="<?php echo $jurusan['id']; ?>" <?php echo $selected_jurusan; ?>>
                                            <?php echo $jurusan['nama_jurusan']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_prodi">Prodi</label>
                                <select class="form-control" id="edit_prodi" name="prodi">
                                    <option value="">-- Pilih Prodi --</option>
                                    <?php foreach ($prodis as $prodi) : ?>
                                        <?php $selected_prodi = ($prodi['id'] == $admin['prodi_id']) ? 'selected' : ''; ?>
                                        <option value="<?php echo $prodi['id']; ?>" <?php echo $selected_prodi; ?>>
                                            <?php echo $prodi['nama_prodi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_tahun_masuk">Tahun Masuk</label>
                                <input type="text" class="form-control" id="edit_tahun_masuk" name="tahun_masuk" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($statuses as $status) : ?>
                                        <?php $selected_status = ($status == $mahasiswa['status']) ? 'selected' : ''; ?>
                                        <option value="<?= htmlspecialchars($status) ?>" <?= $selected_status ?>><?= htmlspecialchars($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_jk">Jenis Kelamin</label>
                                <select class="form-control" id="edit_jk" name="jk" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" <?= ($mahasiswa['jk'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= ($mahasiswa['jk'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_alamat">Alamat</label>
                                <input type="text" class="form-control" id="edit_alamat" name="alamat" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required id="email">
                            </div>
                            <div class="form-group">
                                <label for="edit_no_telp">No. Telp</label>
                                <input type="tel" class="form-control" id="edit_no_telp" name="no_telp" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->


        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="deleteMahasiswaForm" method="post" action="delete_user_mahasiswa.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel"><i class="fas fa-user-minus"></i> Hapus Akun Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_mahasiswa_id" name="mahasiswa_id">
                            <input type="hidden" id="delete_user_id" name="user_id">
                            <p>Anda yakin ingin menghapus akun ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->

    </div><!-- ./wrapper -->
    
    <script src="../script_admin.js"></script>
    <script src="../../app/plugins/jquery/jquery.min.js"></script>
    <script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../app/dist/js/adminlte.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="../../app/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#adminTable').DataTable();

            // Handle edit button click
            $('.btn-edit').on('click', function() {
                const mahasiswaId = $(this).data('id');
                const nim = $(this).data('nim');
                const namaMahasiswa = $(this).data('nama');
                const jurusan = $(this).data('jurusan');
                const prodi = $(this).data('prodi');
                const status = $(this).data('status');
                const tahun_masuk = $(this).data('tahun_masuk');
                const jk = $(this).data('jk');
                const alamat = $(this).data('alamat');
                const email = $(this).data('email');
                const no_telp = $(this).data('no_telp');

                $('#edit_mahasiswa_id').val(mahasiswaId);
                $('#edit_nim').val(nim);
                $('#edit_nama_mahasiswa').val(namaMahasiswa);
                $('#edit_jurusan').val(jurusan);
                $('#edit_prodi').val(prodi);
                $('#edit_status').val(status);
                $('#edit_tahun_masuk').val(tahun_masuk);
                $('#edit_jk').val(jk);
                $('#edit_alamat').val(alamat);
                $('#edit_email').val(email);
                $('#edit_no_telp').val(no_telp);

                $('#editUserModal').modal('show');
            });

            // Handle delete button click
            $('.btn-delete').on('click', function() {
                const mahasiswaId = $(this).data('id');
                const userId = $(this).data('user');

                $('#delete_mahasiswa_id').val(mahasiswaId);
                $('#delete_user_id').val(userId);

                $('#deleteUserModal').modal('show');
            });

            // Handle custom context menu
            $(document).contextmenu(function(event) {
                event.preventDefault();
                var contextMenu = $('#context-menu');
                contextMenu.css({
                    display: 'block',
                    left: event.pageX + 'px',
                    top: event.pageY + 'px'
                });
            });

            // Close context menu on click outside
            $(document).click(function() {
                $('#context-menu').hide();
            });

            //prodi
            $(document).ready(function() {
                $('#jurusan').change(function() {
                    var jurusan_id = $(this).val();
                    if (jurusan_id) {
                        $.ajax({
                            url: 'get_prodi.php',
                            type: 'POST',
                            data: { jurusan_id: jurusan_id },
                            dataType: 'json',
                            success: function(data) {
                                console.log(data); // Tambahkan ini untuk debug
                                $('#prodi').empty();
                                $('#prodi').append('<option value="">-- Pilih Program Studi --</option>');
                                if (data.error) {
                                    alert(data.error);
                                } else {
                                    $.each(data, function(index, value) {
                                        $('#prodi').append('<option value="' + value.id + '">' + value.nama_prodi + '</option>');
                                    });
                                }
                            }
                        });
                    } else {
                        $('#prodi').empty();
                        $('#prodi').append('<option value="">-- Pilih Program Studi --</option>');
                    }
                });
                //edit prodi
                $('#edit_jurusan').change(function() {
                    var jurusan_id = $(this).val();
                    if (jurusan_id) {
                        $.ajax({
                            url: 'get_prodi.php',
                            type: 'POST',
                            data: { jurusan_id: jurusan_id },
                            dataType: 'json',
                            success: function(data) {
                                $('#edit_prodi').empty();
                                $('#edit_prodi').append('<option value="">-- Pilih Prodi --</option>');
                                $.each(data, function(index, value) {
                                    $('#edit_prodi').append('<option value="' + value.id + '">' + value.nama_prodi + '</option>');
                                });
                            }
                        });
                    } else {
                        $('#edit_prodi').empty();
                        $('#edit_prodi').append('<option value="">-- Pilih Prodi --</option>');
                    }
                });

                // Trigger change pada jurusan saat halaman dimuat
                $('#edit_jurusan').trigger('change');
            });

            //email
            document.getElementById('email').addEventListener('input', function(e) {
                const emailField = e.target;
                const email = emailField.value;
                if (!email.includes('@')) {
                    emailField.setCustomValidity('Email harus mengandung karakter "@"');
                } else {
                    emailField.setCustomValidity('');
                }
            });
        });
    </script>

</body>

</html>
