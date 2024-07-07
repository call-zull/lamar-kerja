<?php
session_start();

// Check if user is authenticated as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Fetch admin profile information
$user_id = $_SESSION['user_id'];
$sql = "SELECT a.id, a.nama_admin, a.email, a.profile_image, a.prodi_id, a.jurusan_id, p.nama_prodi AS prodi, j.nama_jurusan AS jurusan
        FROM admins a
        LEFT JOIN prodis p ON a.prodi_id = p.id
        LEFT JOIN jurusans j ON a.jurusan_id = j.id
        WHERE a.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// Check if admin data is found
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    die("Data admin tidak ditemukan.");
}

// Fetch all jurusan and prodi options for dropdowns
$sql_jurusans = "SELECT * FROM jurusans";
$stmt_jurusans = $pdo->prepare($sql_jurusans);
$stmt_jurusans->execute();
$jurusans = $stmt_jurusans->fetchAll(PDO::FETCH_ASSOC);

$sql_prodis = "SELECT * FROM prodis";
$stmt_prodis = $pdo->prepare($sql_prodis);
$stmt_prodis->execute();
$prodis = $stmt_prodis->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <!-- Include your CSS files here -->
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
       .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .btn-bottom-left {
            position: fixed;
            bottom: 20px;
            left: 20px;
        }
        .profile-img {
            max-width: 200px; /* Ubah ukuran gambar di sini */
            max-height: 200px; /* Ubah ukuran gambar di sini */
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Include your navigation bar -->
        <?php include 'navbar_admin.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Profile User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Profile User</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <section class="col-lg-12 connectedSortable">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user"></i>
                                        Profile
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" id="edit-profile-btn">
                                            <i class="fas fa-edit"></i> Edit Profile
                                        </button>
                                        <button type="button" class="btn btn-success d-none" id="save-profile-btn">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="profile-form" action="update_profile.php" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <!-- Display profile image and upload form -->
                                                <?php if (!empty($admin['profile_image'])) : ?>
                                                    <img src="../assets/admin/profile<?php echo $admin['profile_image']; ?>" class="img-fluid mb-3" alt="Admin Image">
                                                <?php else : ?>
                                                    <img src="../assets/images/profile_default.png" class="img-fluid mb-3" alt="Default Image">
                                                <?php endif; ?>
                                                <div class="form-group">
                                                    <label for="fileToUpload" class="form-label">Profile Image</label>
                                                    <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                                            </div>
                                            </div>
                                            <div class="col-md-9">
                                                <!-- Display profile information -->
                                                <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                                <div class="form-group">
                                                    <label for="nama_admin">Nama</label>
                                                    <input type="text" class="form-control profile-input" id="nama_admin" name="nama_admin" value="<?php echo $admin['nama_admin']; ?>" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="jurusan">Jurusan</label>
                                                    <select class="form-control profile-input" id="jurusan" name="jurusan_id" disabled>
                                                        <?php foreach ($jurusans as $jurusan) : ?>
                                                            <option value="<?php echo $jurusan['id']; ?>" <?php echo ($jurusan['id'] == $admin['jurusan_id']) ? 'selected' : ''; ?>><?php echo $jurusan['nama_jurusan']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="prodi">Prodi</label>
                                                    <select class="form-control profile-input" id="prodi" name="prodi_id" disabled>
                                                        <?php foreach ($prodis as $prodi) : ?>
                                                            <option value="<?php echo $prodi['id']; ?>" <?php echo ($prodi['id'] == $admin['prodi_id']) ? 'selected' : ''; ?>><?php echo $prodi['nama_prodi']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control profile-input" id="email" name="email" value="<?php echo $admin['email']; ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="script_admin.js"></script>
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const saveProfileBtn = document.getElementById('save-profile-btn');
        const fileToUpload = document.getElementById('fileToUpload');
        const namaAdminInput = document.getElementById('nama_admin');
        const jurusanSelect = document.getElementById('jurusan');
        const prodiSelect = document.getElementById('prodi');
        const emailInput = document.getElementById('email');

        editProfileBtn.addEventListener('click', function () {
            editProfileBtn.classList.add('d-none');
            saveProfileBtn.classList.remove('d-none');
            enableInputFields();
        });

        saveProfileBtn.addEventListener('click', function () {
            document.getElementById('profile-form').submit();
        });

        fileToUpload.addEventListener('change', function () {
            if (fileToUpload.files.length > 0) {
                saveProfileBtn.classList.remove('d-none');
            }
        });

        function enableInputFields() {
            namaAdminInput.removeAttribute('disabled');
            jurusanSelect.removeAttribute('disabled');
            prodiSelect.removeAttribute('disabled');
            emailInput.removeAttribute('disabled');
        }
    </script>
</body>

</html>
