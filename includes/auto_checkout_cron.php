<?php
include 'config.php';

date_default_timezone_set('Asia/Jakarta');

$log_message = "[" . date('Y-m-d H:i:s') . "] Cron job auto checkout dijalankan.\n";
file_put_contents('../cron_log.txt', $log_message, FILE_APPEND);

// Kueri untuk check-out semua visitor yang:
// 1. Belum check-out (checkout_time IS NULL)
// 2. Waktu check-in-nya adalah hari ini (DATE(checkin_time) = CURDATE())
$sql = "UPDATE visitors SET checkout_time = NOW() WHERE checkout_time IS NULL AND DATE(checkin_time) = CURDATE()";

if ($conn->query($sql) === TRUE) {
    $affected_rows = $conn->affected_rows;
    $log_message = "[" . date('Y-m-d H:i:s') . "] Sukses: " . $affected_rows . " visitor di-checkout secara otomatis.\n";
    file_put_contents('../cron_log.txt', $log_message, FILE_APPEND);
} else {
    // Mencatat jika terjadi error
    $log_message = "[" . date('Y-m-d H:i:s') . "] Error: " . $conn->error . "\n";
    file_put_contents('../cron_log.txt', $log_message, FILE_APPEND);
}

$conn->close();

echo "Proses auto checkout selesai.";
?>