<?php
session_start();
session_destroy();
header('Location: ../pages/dashboard.php');
exit;
?>
