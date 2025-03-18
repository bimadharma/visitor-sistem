<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $NoTelepon = mysqli_real_escape_string($conn, $_POST['NoTelepon']);
    $Kegiatan = mysqli_real_escape_string($conn, $_POST['Kegiatan']);
    $Perusahaan = mysqli_real_escape_string($conn, $_POST['Perusahaan']);

    // Jika check-in kosong, isi otomatis dengan NOW()
    $checkin = empty($_POST['checkin_time']) ? "NOW()" : "'" . mysqli_real_escape_string($conn, $_POST['checkin_time']) . "'";
    
    // Jika check-out kosong, biarkan NULL
    $checkout = empty($_POST['checkout_time']) ? "NULL" : "'" . mysqli_real_escape_string($conn, $_POST['checkout_time']) . "'";

    $sql = "UPDATE visitors SET 
                name='$name', 
                NoTelepon='$NoTelepon', 
                Kegiatan='$Kegiatan', 
                Perusahaan='$Perusahaan', 
                checkin_time=$checkin, 
                checkout_time=$checkout 
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: history.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
