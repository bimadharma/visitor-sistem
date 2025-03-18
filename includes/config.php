<?php
$conn = new mysqli('localhost', 'root', '', 'visitor_admin_app');
if ($conn->connect_error) {
    die('Database connection error: ' . $conn->connect_error);
}
?>
