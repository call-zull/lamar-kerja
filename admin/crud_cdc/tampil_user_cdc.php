<?php
session_start();
include '../../includes/db.php';

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

// Fetch CDC data from database
$sql = "SELECT id, user_id, nama_cdc, alamat_cdc, email_cdc, profile_image FROM cdcs";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cdcs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil User CDC</title>
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

        .profile-img {
            max-width: 100px;
            max-height: 100px;
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
                            <h1 class="m-0">Tampil User CDC</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Tampil User CDC</li>
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
                                        Data CDC
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                                            <i class="fas fa-user-plus"></i> Tambah Akun
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="cdcTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama CDC</th>
                                                    <th>Alamat</th>
                                                    <th>Email</th>
                                                    <th>Profile</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cdcs as $index => $cdc) : ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($cdc['nama_cdc']); ?></td>
                                                        <td><?php echo htmlspecialchars($cdc['alamat_cdc']); ?></td>
                                                        <td><?php echo htmlspecialchars($cdc['email_cdc']); ?></td>
                                                        <td>
                                                            <?php if (!empty($cdc['profile_image'])) : ?>
                                                                <img src="../assets/cdc/profile/<?php echo $cdc['profile_image']; ?>" class="img-thumbnail profile-img" alt="Profile Image">
                                                            <?php else : ?>
                                                                No Image
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary btn-edit" data-id="<?php echo $cdc['id']; ?>" data-nama="<?php echo htmlspecialchars($cdc['nama_cdc']); ?>" data-email="<?php echo htmlspecialchars($cdc['email_cdc']); ?>" data-alamat="<?php echo htmlspecialchars($cdc['alamat_cdc']); ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $cdc['id']; ?>" data-user="<?php echo $cdc['user_id']; ?>">
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
                    <form id="addCDCForm" method="post" action="process_add_cdc.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah Akun CDC Baru</h5>
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
                                <label for="nama_cdc">Nama CDC</label>
                                <input type="text" class="form-control" id="nama_cdc" name="nama_cdc" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat_cdc">Alamat CDC</label>
                                <input type="text" class="form-control" id="alamat_cdc" name="alamat_cdc" required>
                            </div>
                            <div class="form-group">
                                <label for="email_cdc">Email CDC</label>
                                <input type="email" class="form-control" id="email_cdc" name="email_cdc" placeholder="harus mengandung karakter @" required>
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
                    <form id="editCDCForm" method="post" action="update_user_cdc.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit Akun CDC</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_cdc_id" name="cdc_id">
                            <div class="form-group">
                                <label for="edit_nama_cdc">Nama CDC</label>
                                <input type="text" class="form-control" id="edit_nama_cdc" name="nama_cdc" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_email_cdc">Email CDC</label>
                                <input type="email" class="form-control" id="edit_email_cdc" name="email_cdc" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_alamat_cdc">Alamat CDC</label>
                                <input type="text" class="form-control" id="edit_alamat_cdc" name="alamat_cdc" required>
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
                    <form id="deleteCDCForm" method="post" action="delete_user_cdc.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Hapus Akun CDC</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_cdc_id" name="cdc_id">
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
                $('#cdcTable').DataTable();

                // Handle edit button click
                $('.btn-edit').on('click', function() {
                    const cdcId = $(this).data('id');
                    const namaCDC = $(this).data('nama');
                    const emailCDC = $(this).data('email');
                    const alamatCDC = $(this).data('alamat');

                    $('#edit_cdc_id').val(cdcId);
                    $('#edit_nama_cdc').val(namaCDC);
                    $('#edit_email_cdc').val(emailCDC);
                    $('#edit_alamat_cdc').val(alamatCDC);

                    $('#editUserModal').modal('show');
                });

                // Handle delete button click
                $('.btn-delete').on('click', function() {
                    const cdcId = $(this).data('id');
                    const userId = $(this).data('user');

                    $('#delete_cdc_id').val(cdcId);
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
            });
        </script>
    </div>
</body>

</html>
