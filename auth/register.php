<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - SI Portofolio POLIBAN</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <style>
        .register-page {
            background-image: url('../assets/images/poliban_background.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .register-box {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px;
        }
        .logo {
            width: 80px;
        }
    </style>
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="../app/index.html" class="h4"><b>SI</b>Portofolio</a>
                <p><img src="../assets/images/logo_poliban.png" alt="POLIBAN Logo" class="logo"></p>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Daftar Akun Baru</p>
                <?php
                session_start();
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']);
                }
                ?>
                <form action="../auth/process_register.php" method="post">
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
                    <div class="input-group mb-3">
                        <select class="form-control" name="role" id="role" required>
                            <option value="" disabled selected>Pilih role</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="perusahaan">Perusahaan</option>
                        </select>
                    </div>
                    <div class="input-group mb-3" id="nim-field" style="display: none;">
                        <input type="text" class="form-control" placeholder="NIM" name="nim">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3" id="whatsapp-field" style="display: none;">
                        <input type="text" class="form-control" placeholder="No. WhatsApp untuk konfirmasi akun" name="no_telp">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fab fa-whatsapp"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </div>
                </form>
                <p class="mb-1">
                    <a href="login.php">Sudah punya akun? Login</a>
                </p>
            </div>
        </div>
    </div>
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#role').change(function() {
                if ($(this).val() == 'mahasiswa') {
                    $('#nim-field').show();
                    $('#whatsapp-field').show();
                } else if ($(this).val() == 'perusahaan') {
                    $('#nim-field').hide();
                    $('#whatsapp-field').show();
                } else {
                    $('#nim-field').hide();
                    $('#whatsapp-field').hide();
                }
            });

            // Disable form submission on reload
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }
        });
    </script>
</body>
</html>
