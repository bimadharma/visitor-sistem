<?php
// Koneksi ke database
require_once '../includes/config.php';

// Ambil parameter tanggal dari URL
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Pastikan tanggal dalam format yang tepat sebelum digunakan dalam query
if ($startDate && $endDate) {
    // Pastikan tanggal berada dalam format MySQL (YYYY-MM-DD)
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate = date('Y-m-d', strtotime($endDate));

    // Query ambil data riwayat pengunjung dengan filter tanggal jika ada
    // Pastikan untuk menggunakan DATE() untuk mengabaikan bagian waktu
$query = "SELECT name, NoTelepon, Kegiatan, Perusahaan, PIC, Ticket, checkin_time, checkout_time
FROM visitors
WHERE DATE(checkin_time) BETWEEN ? AND ?
ORDER BY checkin_time ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Jika tidak ada filter, ambil semua data
    $query = "SELECT name, NoTelepon, Kegiatan, Perusahaan, PIC, Ticket, checkin_time, checkout_time 
              FROM visitors 
              ORDER BY checkin_time ASC";
    $result = $conn->query($query);
}

// Header untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=history_pengunjung.csv');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom ke file CSV
fputcsv($output, array('No', 'Nama', 'NoTelepon', 'Kegiatan', 'Perusahaan', 'PIC', 'Ticket', 'Check-in Time', 'Check-out Time'));

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
