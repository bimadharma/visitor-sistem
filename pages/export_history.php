<?php

// Koneksi ke database
require_once '../includes/config.php';

// Query ambil data riwayat pengunjung
$query = "SELECT name, NoTelepon, Kegiatan, Perusahaan, checkin_time, checkout_time FROM visitors ORDER BY checkin_time ASC";
$result = $conn->query($query);

// Header untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=history_pengunjung.csv');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom ke file CSV
fputcsv($output, array('No', 'Nama', 'NoTelepon', 'Kegiatan', 'Perusahaan', 'Check-in Time', 'Check-out Time'));

// Tulis data baris per baris ke CSV dengan nomor urut
$no = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_unshift($row, $no); // Tambahkan nomor urut di awal array
        fputcsv($output, $row);
        $no++;
    }
} else {
    // Jika data kosong, tetap tampilkan header
    fputcsv($output, array('No data available'));
}

// Tutup koneksi dan output stream
fclose($output);
$conn->close();
exit();
?>
