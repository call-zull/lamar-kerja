<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SI Portofolio POLIBAN</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <style>
        .login-page {
            background-image: url('../assets/images/poliban_background.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .login-box {
            background-color: rgba(255, 255, 255, 0.8); /* Tambahkan transparansi */
            border-radius: 10px; /* Tambahkan radius sudut */
            padding: 20px; /* Tambahkan padding */
        }
        .logo {
            width: 80px; /* Ubah ukuran logo sesuai kebutuhan */
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="../app/index.html" class="h4"><b>SI</b>Portofolio</a>
                <p><img src="../assets/images/logo_poliban.png" alt="POLIBAN Logo" class="logo"></p>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Login ke akun Anda</p>
                <?php
                session_start();
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <form action="../auth/process_login.php" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" name="username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </div>
                </form>
                <p class="mb-1">
                    <a href="lupa_password.php">Lupa password?</a>
                </p>
            </div>
        </div>
    </div>


    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
</body>
</html>
