<?php
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';

$history = $conn->query("SELECT * FROM visitors");
?>

<!-- Tambahkan CSS DataTables dan Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<h3 class="text-center py-4">Visitor History</h3>
<div class="container">
    <!-- Search Box Custom -->
    <div id="customSearch" class="mb-3"></div>

    <!-- Tombol Download & Filter -->
    <div class="d-flex justify-content-between mb-3">
        <a href="export_history.php" class="btn btn-primary">
            <i class="bi bi-download"></i> Download History
        </a>

    </div>

  <!-- Tambahkan Kolom Aksi di Tabel -->
<table id="visitorTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>No Telepon</th>
            <th>Kegiatan</th>
            <th>Perusahaan</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while ($row = $history->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['NoTelepon'] ?></td>
                <td><?= $row['Kegiatan'] ?></td>
                <td><?= $row['Perusahaan'] ?></td>
                <td><?= $row['checkin_time'] ?></td>
                <td><?= $row['checkout_time'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm viewDetail" 
                            data-name="<?= $row['name'] ?>" 
                            data-telepon="<?= $row['NoTelepon'] ?>" 
                            data-kegiatan="<?= $row['Kegiatan'] ?>"
                            data-perusahaan="<?= $row['Perusahaan'] ?>"
                            data-foto_diri="<?= $row['foto_diri'] ?>"
                            data-foto_ktp="<?= $row['foto_ktp'] ?>"
                            data-checkin="<?= $row['checkin_time'] ?>"
                            data-checkout="<?= $row['checkout_time'] ?>"
                            data-bs-toggle="modal" data-bs-target="#viewModal">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal View Detail -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Detail Visitor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="modalName"></span></p>
                <p><strong>No.Telepon:</strong> <span id="modalTelepon"></span></p>
                <p><strong>Kegiatan:</strong> <span id="modalKegiatan"></span></p>
                <p><strong>Perusahaan:</strong> <span id="modalPerusahaan"></span></p>
                <p><strong>Check-in:</strong> <span id="modalCheckin"></span></p>
                <p><strong>Check-out:</strong> <span id="modalCheckout"></span></p>
                <p><strong>Foto Diri:</strong></p>
                <img id="modalFotoDiri" src="" class="img-fluid" alt="Foto Diri">
                <p><strong>Foto KTP:</strong></p>
                <img id="modalFotoKTP" src="" class="img-fluid" alt="Foto KTP">
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk Menampilkan Data di Modal -->
<!-- DataTables JS + CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('.viewDetail').on('click', function() {
            $('#modalName').text($(this).data('name'));
            $('#modalTelepon').text($(this).data('telepon'));
            $('#modalKegiatan').text($(this).data('kegiatan'));
            $('#modalPerusahaan').text($(this).data('perusahaan'));
            $('#modalCheckin').text($(this).data('checkin'));
            $('#modalCheckout').text($(this).data('checkout'));

            // Menampilkan gambar
            $('#modalFotoDiri').attr('src', window.location.origin + '/Visitor-web/' + $(this).data('foto_diri'));
            $('#modalFotoKTP').attr('src', window.location.origin + '/Visitor-web/' + $(this).data('foto_ktp'));


        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#visitorTable').DataTable({
            responsive: true,
            lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
            "pageLength": -1,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: { first: "Pertama", last: "Terakhir", next: "Berikutnya", previous: "Sebelumnya" },
                zeroRecords: "Tidak ada data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)"
            }
        });
        $('#visitorTable_filter input').attr('placeholder', 'Search visitors...');

    });
</script>

