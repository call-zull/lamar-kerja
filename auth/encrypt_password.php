<?php
$password = 'perusahaan'; // Ganti dengan password yang ingin Anda enkripsi
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>