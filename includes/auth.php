<?php
session_start();

// Jika tidak ada sesi user atau bukan admin, redirect ke login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /Visitor-web/auth/login.php");
    exit;
}

// Auto logout setelah 10 menit tidak aktif
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) { 
    session_unset();  
    session_destroy();  
    header("Location: /Visitor-web/auth/login.php");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Perbarui waktu terakhir aktif
?>
