<?php
session_start();

// Check if user is authenticated as CDC
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cdc') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Fetch CDC profile information
$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id, c.user_id, c.nama_cdc, c.alamat_cdc, c.email_cdc, c.profile_image
        FROM cdcs c
        WHERE c.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// Check if CDC data is found
$cdc = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cdc) {
    die("Data CDC tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDC Profile</title>
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
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Include your navigation bar -->
        <?php include 'navbar_cdc.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Profile CDC</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Profile CDC</li>
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
                                                <?php if (!empty($cdc['profile_image'])) : ?>
                                                    <img src="../assets/cdc/profile<?php echo $cdc['profile_image']; ?>" class="img-fluid mb-3" alt="CDC Image">
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
                                                <input type="hidden" name="id" value="<?php echo $cdc['id']; ?>">
                                                <div class="form-group">
                                                    <label for="nama_cdc">Nama CDC</label>
                                                    <input type="text" class="form-control profile-input" id="nama_cdc" name="nama_cdc" value="<?php echo $cdc['nama_cdc']; ?>" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="alamat_cdc">Alamat CDC</label>
                                                    <input type="text" class="form-control profile-input" id="alamat_cdc" name="alamat_cdc" value="<?php echo $cdc['alamat_cdc']; ?>" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email_cdc">Email CDC</label>
                                                    <input type="email" class="form-control profile-input" id="email_cdc" name="email_cdc" value="<?php echo $cdc['email_cdc']; ?>" disabled>
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

    <script src="script_cdc.js"></script>
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const saveProfileBtn = document.getElementById('save-profile-btn');
        const fileToUpload = document.getElementById('fileToUpload');
        const namaCdcInput = document.getElementById('nama_cdc');
        const alamatCdcInput = document.getElementById('alamat_cdc');
        const emailCdcInput = document.getElementById('email_cdc');

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
            namaCdcInput.removeAttribute('disabled');
            alamatCdcInput.removeAttribute('disabled');
            emailCdcInput.removeAttribute('disabled');
        }
    </script>
</body>

</html>
