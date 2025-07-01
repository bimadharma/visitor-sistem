<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $NoTelepon = mysqli_real_escape_string($conn, $_POST['NoTelepon']);
    $Kegiatan = mysqli_real_escape_string($conn, $_POST['Kegiatan']);
    $Perusahaan = mysqli_real_escape_string($conn, $_POST['Perusahaan']);
    $Ticket = mysqli_real_escape_string($conn, $_POST['Ticket']);

    // Mulai membangun query
    $sql = "UPDATE visitors SET 
                name='$name', 
                NoTelepon='$NoTelepon', 
                Kegiatan='$Kegiatan', 
                Perusahaan='$Perusahaan',
                Ticket='$Ticket'";

    // Tambahkan checkin_time jika diisi
    if (!empty($_POST['checkin_time'])) {
        $checkin = mysqli_real_escape_string($conn, $_POST['checkin_time']);
        $sql .= ", checkin_time='$checkin'";
    }

    // Tambahkan checkout_time jika diisi
    if (!empty($_POST['checkout_time'])) {
        $checkout = mysqli_real_escape_string($conn, $_POST['checkout_time']);
        $sql .= ", checkout_time='$checkout'";
    }

    // Tambahkan kondisi WHERE
    $sql .= " WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: history.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
