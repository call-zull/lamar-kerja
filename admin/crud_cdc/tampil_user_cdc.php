<?php
session_start();
include '../includes/db.php';

// Periksa apakah pengguna telah login sebagai admin, jika tidak arahkan ke halaman login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Check for success and error message notifications
$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear notification after displaying
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear notification after displaying
}

// Query untuk mendapatkan data perusahaan
$search_query = '';
$where_clause = '';
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
    $where_clause = " WHERE nama_cdc LIKE :search OR alamat_cdc LIKE :search OR email_cdc LIKE :search";
}

$sql = "SELECT * FROM cdcs" . $where_clause;
$stmt = $pdo->prepare($sql);

if (!empty($where_clause)) {
    $stmt->execute(['search' => "%$search_query%"]);
} else {
    $stmt->execute();
}

$cdc_list = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil User CDC</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <!-- Dark Mode -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <!-- Light Mode -->
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
<?php include '../includes/navbar_admin.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tampil User CDC</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Tampil User CDC</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?= $success_message ?>
                    </div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-12 connectedSortable">
                        <!-- Tampil User Admin -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users"></i>
                                    Data CDC
                                </h3>
                                <br><br>
                                <form class="form-inline" method="GET">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                            </div>
                                            <input class="form-control" name="search" type="text" placeholder="Search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                                        </div>
                                    </form>
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
                                                <th>Alamat</th>
                                                <th>Email</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cdc_list as $index => $cdc): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($cdc['nama_cdc']) ?></td>
                                                <td><?= htmlspecialchars($cdc['alamat_cdc']) ?></td>
                                                <td><?= htmlspecialchars($cdc['email_cdc']) ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="<?= $cdc['id'] ?>" data-nama="<?= htmlspecialchars($cdc['nama_cdc']) ?>" data-alamat="<?= htmlspecialchars($cdc['alamat_cdc']) ?>" data-email="<?= htmlspecialchars($cdc['email_cdc']) ?>"><i class="fas fa-edit"></i> Edit</button>
                                                    <a href="delete_user_cdc.php?id=<?= $cdc['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>



                        <!-- Modal Tambah User -->
                        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form id="addUserForm" action="../auth/process_add_cdc.php" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus"></i> Tambah Akun CDC</h5>
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
                                                <label for="nama_cdc">Nama</label>
                                                <input type="text" class="form-control" id="nama_cdc" name="nama_cdc" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="alamat_cdc">Alamat</label>
                                                <textarea class="form-control" id="alamat_cdc" name="alamat_cdc" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="email_cdc">Email</label>
                                                <input type="email" class="form-control" id="email_cdc" name="email_cdc" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.modal -->

                        <!-- Modal Update User -->
                        <div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="updateUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateUserModalLabel">Edit Akun CDC</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="update_user_cdc.php" method="POST">
                                            <input type="hidden" id="update_id" name="id">
                                            <div class="form-group">
                                                <label for="update_nama_cdc">Nama cdc</label>
                                                <input type="text" class="form-control" id="update_nama_cdc" name="nama_cdc" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_alamat_cdc">Alamat cdc</label>
                                                <textarea class="form-control" id="update_alamat_cdc" name="alamat_cdc" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="update_email_cdc">Email cdc</label>
                                                <input type="email" class="form-control" id="update_email_cdc" name="email_cdc" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.modal -->

                        <!-- Modal Hapus User -->
                        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteUserModalLabel">Hapus Akun CDC</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus akun ini?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <a href="#" id="deleteUserBtn" class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.modal -->
                    </section>
                    <!-- /.Left col -->
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <footer class="main-footer">
        <strong>&copy; 2024 <a href="#"></a>.</strong>
        All rights reserved.
        <!-- <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.0.5
        </div> -->
    </footer>
    <!-- /.footer -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../app/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../app/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../app/dist/js/demo.js"></script>
<script>
    $(document).ready(function() {
        // Edit button click handler
        $('.btn-edit').on('click', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const alamat = $(this).data('alamat');
            const email = $(this).data('email');
            
            $('#update_id').val(id);
            $('#update_nama_cdc').val(nama);
            $('#update_alamat_cdc').val(alamat);
            $('#update_email_cdc').val(email);
            
            $('#updateUserModal').modal('show');
        });

        // Delete button click handler
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            $('#deleteUserBtn').attr('href', 'delete_user_cdc.php?id=' + id);
            $('#deleteUserModal').modal('show');
        });
    });
</script>
</body>
</html>
