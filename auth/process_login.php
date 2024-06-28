<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        // Redirect berdasarkan peran pengguna
        switch ($user['role']) {
            case 'admin':
                header('Location: ../admin/index.php');
                break;
            case 'cdc':
                header('Location: ../cdc/index.php');
                break;
            case 'mahasiswa':
                header('Location: ../mahasiswa/index.php');
                break;
            case 'perusahaan':
                header('Location: ../perusahaan/index.php');
                break;
            default:
                $_SESSION['error'] = 'Peran pengguna tidak valid';
                header('Location: login.php');
                break;
        }
        exit;
        } else {
        $_SESSION['error'] = 'Invalid username or password';
        header('Location: login.php');
        exit;
        }
        } else {
        header('Location: login.php');
        exit;
        }
?>

//         // Jika username dan password adalah "admin,admin", arahkan langsung ke halaman admin
//         if ($username === 'admin' && $password === 'admin') {
//             header('Location: ../admin/index.php');
//             exit;
//         }
//         // Pengecekan untuk cdc
//         if ($username === 'cdc' && $password === 'cdc') {
//             $_SESSION['user_id'] = 2; // ID CDC bisa diisi sesuai keinginan
//             $_SESSION['role'] = 'cdc';
//             header('Location: ../cdc/index.php');
//             exit;
//         }
//         if ($username === 'mahasiswa' && $password === 'mahasiswa') {
//             $_SESSION['user_id'] = 3; // ID CDC bisa diisi sesuai keinginan
//             $_SESSION['role'] = 'mahasiswa';
//             header('Location: ../mahasiswa/index.php');
//             exit;
//         }
//         if ($username === 'perusahaan' && $password === 'perusahaan') {
//             $_SESSION['user_id'] = 4; // ID CDC bisa diisi sesuai keinginan
//             $_SESSION['role'] = 'perusahaan';
//             header('Location: ../perusahaan/index.php');
//             exit;
//         }
//         // Redirect ke index.php yang akan mengarahkan ke dashboard yang tepat
//         header('Location: ../index.php');
//         exit;
//     } else {
//         $_SESSION['error'] = 'Invalid username or password';
//         header('Location: login.php');
//         exit;
//     }
// } else {
//     header('Location: login.php');
//     exit;
// }

