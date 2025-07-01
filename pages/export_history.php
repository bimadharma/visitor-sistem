<?php
// Koneksi ke database
require_once '../includes/config.php';

// Ambil parameter tanggal dari URL dan bersihkan input
$startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : '';
$endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : '';

// Pastikan tanggal dalam format yang tepat sebelum digunakan dalam query
$useDateFilter = false;
if (!empty($startDate) && !empty($endDate)) {
    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);

    if ($startTimestamp && $endTimestamp) {
        $startDate = date('Y-m-d', $startTimestamp);
        $endDate = date('Y-m-d', $endTimestamp);
        $useDateFilter = true;
    }
}

// Query ambil data riwayat pengunjung
if ($useDateFilter) {
    $query = "SELECT  tanggal, name, NoTelepon, Kegiatan, Perusahaan, PIC, Ticket, checkin_time, checkout_time
              FROM visitors
              WHERE DATE(tanggal) BETWEEN ? AND ?
              ORDER BY tanggal ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT tanggal, name, NoTelepon, Kegiatan, Perusahaan, PIC, Ticket, checkin_time, checkout_time 
              FROM visitors 
              ORDER BY tanggal ASC";
    $result = $conn->query($query);
}

// Header untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=history_pengunjung.csv');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom ke file CSV
fputcsv($output, array('No','Tanggal','Nama', 'No. Telepon', 'Kegiatan', 'Perusahaan', 'PIC', 'Ticket Helpdesk', 'Check-in Time', 'Check-out Time'));

// Tulis data baris per baris ke CSV dengan nomor urut
$no = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
          // Format waktu checkin_time dan checkout_time ke format jam:menit
          $checkinTime = date('H:i', strtotime($row['checkin_time']));
          $checkoutTime = date('H:i', strtotime($row['checkout_time']));

        $data = array_merge([$no], [
            $row['tanggal'],
            $row['name'],
            $row['NoTelepon'],
            $row['Kegiatan'],
            $row['Perusahaan'],
            $row['PIC'],
            $row['Ticket'],
            $checkinTime,     
            $checkoutTime 
        ]);
        fputcsv($output, $data);
        $no++;
    }
} else {
    // Jika data kosong, tetap tampilkan header kosong
    fputcsv($output, array('No data available'));
}

// Tutup koneksi dan output stream
fclose($output);
$conn->close();
exit;
?>
