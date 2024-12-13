<?php
// Memulai session
session_start();

// Menghapus semua session
session_unset();

// Menghancurkan session
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: Login.php");
exit;
?>
