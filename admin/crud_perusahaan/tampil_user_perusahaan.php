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

// Fetch perusahaan data from database
$sql = "SELECT id, user_id, nama_perusahaan, alamat_perusahaan, email_perusahaan, profile_image FROM perusahaans";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$perusahaans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampil User Perusahaan</title>
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
                            <h1 class="m-0">Tampil User Perusahaan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Tampil User Perusahaan</li>
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
                                        Data Perusahaan
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                                            <i class="fas fa-user-plus"></i> Tambah Akun
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="perusahaanTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Perusahaan</th>
                                                    <th>Alamat</th>
                                                    <th>Email</th>
                                                    <th>Profile</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($perusahaans as $index => $perusahaan) : ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?></td>
                                                        <td><?php echo htmlspecialchars($perusahaan['alamat_perusahaan']); ?></td>
                                                        <td><?php echo htmlspecialchars($perusahaan['email_perusahaan']); ?></td>
                                                        <td>
                                                            <?php if (!empty($perusahaan['profile_image'])) : ?>
                                                                <img src="../../assets/perusahaan/profile/<?php echo $perusahaan['profile_image']; ?>" class="img-thumbnail profile-img" alt="Profile Image">
                                                            <?php else : ?>
                                                                No Image
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary btn-edit" data-id="<?php echo $perusahaan['id']; ?>" data-nama="<?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?>" data-email="<?php echo htmlspecialchars($perusahaan['email_perusahaan']); ?>" data-alamat="<?php echo htmlspecialchars($perusahaan['alamat_perusahaan']); ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $perusahaan['id']; ?>" data-user="<?php echo $perusahaan['user_id']; ?>">
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
                    <form id="addPerusahaanForm" method="post" action="process_add_perusahaan.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah Akun Perusahaan Baru</h5>
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
                                <label for="nama_perusahaan">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat_perusahaan">Alamat Perusahaan</label>
                                <input type="text" class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label for="email_perusahaan">Email Perusahaan</label>
                                <input type="email" class="form-control" id="email_perusahaan" name="email_perusahaan" required>
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
                    <form id="editPerusahaanForm" method="post" action="update_user_perusahaan.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit Akun Perusahaan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_perusahaan_id" name="perusahaan_id">
                            <div class="form-group">
                                <label for="edit_nama_perusahaan">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="edit_nama_perusahaan" name="nama_perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_email_perusahaan">Email Perusahaan</label>
                                <input type="email" class="form-control" id="edit_email_perusahaan" name="email_perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_alamat_perusahaan">Alamat Perusahaan</label>
                                <input type="text" class="form-control" id="edit_alamat_perusahaan" name="alamat_perusahaan" required>
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
                    <form id="deletePerusahaanForm" method="post" action="delete_user_perusahaan.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Hapus Akun Perusahaan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_perusahaan_id" name="perusahaan_id">
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
                $('#perusahaanTable').DataTable();

                // Handle edit button click
                $('.btn-edit').on('click', function() {
                    const perusahaanId = $(this).data('id');
                    const namaPerusahaan = $(this).data('nama');
                    const emailPerusahaan = $(this).data('email');
                    const alamatPerusahaan = $(this).data('alamat');

                    $('#edit_perusahaan_id').val(perusahaanId);
                    $('#edit_nama_perusahaan').val(namaPerusahaan);
                    $('#edit_email_perusahaan').val(emailPerusahaan);
                    $('#edit_alamat_perusahaan').val(alamatPerusahaan);

                    $('#editUserModal').modal('show');
                });

                // Handle delete button click
                $('.btn-delete').on('click', function() {
                    const perusahaanId = $(this).data('id');
                    const userId = $(this).data('user');

                    $('#delete_perusahaan_id').val(perusahaanId);
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
