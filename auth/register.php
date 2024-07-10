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
                <form action="../auth/process_register.php" method="post" id="registerForm">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" name="username" id="username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password" id="password" required>
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
                        <input type="text" class="form-control" placeholder="NIM" name="nim" id="nim">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3" id="nama-mahasiswa-field" style="display: none;">
                        <input type="text" class="form-control" placeholder="Nama Mahasiswa" name="nama_mahasiswa" id="nama_mahasiswa">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3" id="jurusan-field" style="display: none;">
                        <select class="form-control" name="jurusan_id" id="jurusan_id">
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <?php
                            include('../includes/db.php');
                            $stmt = $pdo->query("SELECT id, nama_jurusan FROM jurusans");
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . $row['id'] . '">' . $row['nama_jurusan'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group mb-3" id="prodi-field" style="display: none;">
                        <select class="form-control" name="prodi_id" id="prodi_id">
                            <option value="" disabled selected>Pilih Prodi</option>
                            <!-- Prodi options will be populated based on selected Jurusan via AJAX -->
                        </select>
                    </div>
                    <div class="input-group mb-3" id="whatsapp-field" style="display: none;">
                        <input type="text" class="form-control" placeholder="No. WhatsApp untuk konfirmasi akun" name="no_telp" id="no_telp">
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
            // Show/hide fields based on selected role
            $('#role').change(function() {
                if ($(this).val() == 'mahasiswa') {
                    $('#nim-field').show();
                    $('#nama-mahasiswa-field').show();
                    $('#jurusan-field').show();
                    $('#prodi-field').show();
                    $('#whatsapp-field').show();
                } else if ($(this).val() == 'perusahaan') {
                    $('#nim-field').hide();
                    $('#nama-mahasiswa-field').hide();
                    $('#jurusan-field').hide();
                    $('#prodi-field').hide();
                    $('#whatsapp-field').show();
                } else {
                    $('#nim-field').hide();
                    $('#nama-mahasiswa-field').hide();
                    $('#jurusan-field').hide();
                    $('#prodi-field').hide();
                    $('#whatsapp-field').hide();
                }
            });

            // Fetch prodi options based on selected jurusan
            $('#jurusan_id').change(function() {
                var jurusan_id = $(this).val();
                $.ajax({
                    url: 'get_prodi.php',
                    method: 'POST',
                    data: { jurusan_id: jurusan_id },
                    success: function(data) {
                        $('#prodi_id').html(data);
                    }
                });
            });

            // Client-side form validation
            $('#registerForm').submit(function(e) {
                var username = $('#username').val().trim();
                var password = $('#password').val().trim();
                var role = $('#role').val();
                var nim = $('#nim').val().trim();
                var nama_mahasiswa = $('#nama_mahasiswa').val().trim();
                var jurusan_id = $('#jurusan_id').val();
                var prodi_id = $('#prodi_id').val();
                var no_telp = $('#no_telp').val().trim();

                if (username === '') {
                    alert('Username harus diisi');
                    e.preventDefault();
                    return false;
                }

                if (password === '') {
                    alert('Password harus diisi');
                    e.preventDefault();
                    return false;
                }

                if (role === '') {
                    alert('Role harus dipilih');
                    e.preventDefault();
                    return false;
                }

                if (role === 'mahasiswa') {
                    if (nim === '') {
                        alert('NIM harus diisi untuk mahasiswa');
                        e.preventDefault();
                        return false;
                    }
                    if (nama_mahasiswa === '') {
                        alert('Nama Mahasiswa harus diisi');
                        e.preventDefault();
                        return false;
                    }
                    if (jurusan_id === null) {
                        alert('Jurusan harus dipilih untuk mahasiswa');
                        e.preventDefault();
                        return false;
                    }
                    if (prodi_id === null) {
                        alert('Prodi harus dipilih untuk mahasiswa');
                        e.preventDefault();
                        return false;
                    }
                }

                if (no_telp === '') {
                    alert('Nomor WhatsApp harus diisi');
                    e.preventDefault();
                    return false;
                }
            });

            // Disable form resubmission on page reload
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
</body>
</html>
