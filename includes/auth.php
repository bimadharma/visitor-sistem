<?php
session_start();

// Jika tidak ada sesi user, redirect ke login
if (!isset($_SESSION['user'])) {
    header("Location: /Visitor-web/auth/login.php");
    exit;
}

// Auto logout setelah 30 menit tidak aktif
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) { 
    session_unset();  
    session_destroy();  
    header("Location: /Visitor-web/auth/login.php");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); 
?>
