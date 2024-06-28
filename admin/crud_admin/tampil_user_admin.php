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

// Redirect to login if not logged in as admin
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

// Fetch admin data from database
$sql = "SELECT a.id, a.nama_admin, a.email, a.profile_image, p.nama_prodi, j.nama_jurusan, u.id as user_id, a.prodi_id, a.jurusan_id
        FROM admins a 
        JOIN users u ON a.user_id = u.id
        LEFT JOIN prodis p ON a.prodi_id = p.id
        LEFT JOIN jurusans j ON a.jurusan_id = j.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil User Admin</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
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
                            <h1 class="m-0">Tampil User Admin</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Tampil User Admin</li>
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
                                        Data Admin
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
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Profile</th>
                                                    <th>Prodi</th>
                                                    <th>Jurusan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($admins as $index => $admin) : ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($admin['nama_admin']); ?></td>
                                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                                        <td>
                                                            <?php if (!empty($admin['profile_image'])) : ?>
                                                                <img src="../assets/admin/<?php echo $admin['profile_image']; ?>" class="img-thumbnail" style="max-width: 50px;">
                                                            <?php else : ?>
                                                                No Image
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo isset($admin['nama_prodi']) ? htmlspecialchars($admin['nama_prodi']) : ''; ?></td>
                                                        <td><?php echo isset($admin['nama_jurusan']) ? htmlspecialchars($admin['nama_jurusan']) : ''; ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary btn-edit" data-id="<?php echo $admin['id']; ?>" data-nama="<?php echo htmlspecialchars($admin['nama_admin']); ?>" data-email="<?php echo htmlspecialchars($admin['email']); ?>" data-jurusan="<?php echo isset($admin['jurusan_id']) ? $admin['jurusan_id'] : ''; ?>" data-prodi="<?php echo isset($admin['prodi_id']) ? $admin['prodi_id'] : ''; ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $admin['id']; ?>" data-user="<?php echo $admin['user_id']; ?>">
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
                    <form id="addAdminForm" method="post" action="process_add_admin.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah Akun Admin Baru</h5>
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
                                <label for="nama_admin">Nama Admin</label>
                                <input type="text" class="form-control" id="nama_admin" name="nama_admin" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="harus mengandung karakter @" required id="email">
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
                                    <!-- php foreach ($prodis as $prodi) : ?>
                                        <option value="php echo $prodi['id']; ?>"><php echo $prodi['nama_prodi']; ?></option>
                                    <php endforeach; ?> -->
                                </select>
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
                    <form id="editAdminForm" method="post" action="update_user_admin.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit Akun Admin</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_admin_id" name="admin_id">
                            <div class="form-group">
                                <label for="edit_nama_admin">Nama Admin</label>
                                <input type="text" class="form-control" id="edit_nama_admin" name="nama_admin" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required id="email">
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="deleteAdminForm" method="post" action="delete_user_admin.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Hapus Akun Admin</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_admin_id" name="admin_id">
                            <input type="hidden" id="delete_user_id" name="user_id">
                            <p>Apakah anda yakin ingin menghapus akun ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="../script_admin.js"></script>
        <script src="../../app/plugins/jquery/jquery.min.js"></script>
        <script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../../app/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="../../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="../../app/dist/js/adminlte.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                $('#adminTable').DataTable();

                // Handle edit button click
                $('.btn-edit').on('click', function() {
                    const adminId = $(this).data('id');
                    const namaAdmin = $(this).data('nama');
                    const email = $(this).data('email');
                    const jurusan = $(this).data('jurusan');
                    const prodi = $(this).data('prodi');

                    $('#edit_admin_id').val(adminId);
                    $('#edit_nama_admin').val(namaAdmin);
                    $('#edit_email').val(email);
                    $('#edit_jurusan').val(jurusan);
                    $('#edit_prodi').val(prodi);

                    $('#editUserModal').modal('show');
                });

                // Handle delete button click
                $('.btn-delete').on('click', function() {
                    const adminId = $(this).data('id');
                    const userId = $(this).data('user');

                    $('#delete_admin_id').val(adminId);
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
                document.getElementById('email').addEventListener('input', function (e) {
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
    </div>
</body>

</html>
