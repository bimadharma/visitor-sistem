<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $sql = "DELETE FROM visitors WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: history.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
