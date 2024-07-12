<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mendapatkan pengguna berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verifikasi pengguna dan password
    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: ../admin/index.php');
            exit;
        } elseif ($user['role'] === 'cdc') {
            $stmtCDC = $pdo->prepare("SELECT approved FROM cdcs WHERE user_id = :user_id");
            $stmtCDC->execute(['user_id' => $user['id']]);
            $cdc = $stmtCDC->fetch();

            if ($cdc && $cdc['approved'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['cdc_id'] = $user['id'];
                header('Location: ../cdc/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
                header('Location: login.php');
                exit;
            }
        } elseif ($user['role'] === 'mahasiswa') {
            // Query untuk mendapatkan ID mahasiswa dan status persetujuan berdasarkan user_id
            $stmtMahasiswa = $pdo->prepare("SELECT id, approved FROM mahasiswas WHERE user_id = :user_id");
            $stmtMahasiswa->execute(['user_id' => $user['id']]);
            $mahasiswa = $stmtMahasiswa->fetch();

            if ($mahasiswa && $mahasiswa['approved'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['mahasiswa_id'] = $mahasiswa['id'];  // Simpan ID mahasiswa

                // Debugging output
                echo "Logged in as Mahasiswa. User ID: " . $_SESSION['user_id'] . "<br>";
                echo "Mahasiswa ID: " . $_SESSION['mahasiswa_id'] . "<br>";
                exit;

                header('Location: ../mahasiswa/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
                header('Location: login.php');
                exit;
            }
        } elseif ($user['role'] === 'perusahaan') {
            $stmtPerusahaan = $pdo->prepare("SELECT approved FROM perusahaans WHERE user_id = :user_id");
            $stmtPerusahaan->execute(['user_id' => $user['id']]);
            $perusahaan = $stmtPerusahaan->fetch();

            if ($perusahaan && $perusahaan['approved'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['perusahaan_id'] = $user['id'];
                header('Location: ../perusahaan/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
                header('Location: login.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Role tidak dikenal.';
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Username atau password salah';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
