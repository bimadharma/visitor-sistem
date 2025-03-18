<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /Visitor-web/auth/login.php');
    exit;
}
?>
