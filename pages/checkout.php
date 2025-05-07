<?php
ob_start();
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';
$current_page = basename($_SERVER['PHP_SELF']);

// Proses Check-Out satu visitor berdasarkan ID
if (isset($_GET['id'])) {
    $conn->query("UPDATE visitors SET checkout_time=NOW() WHERE id={$_GET['id']}");
    header("Location: " . $_SERVER['PHP_SELF'] . "?success");
    exit();
}

// Proses Check-Out semua visitor yang belum check-out
if (isset($_GET['checkout_all'])) {
    $conn->query("UPDATE visitors SET checkout_time=NOW() WHERE checkout_time IS NULL");
    header("Location: " . $_SERVER['PHP_SELF'] . "?success_all");
    exit();
}

// Ambil data visitor yang belum check-out
$visitors = $conn->query("SELECT * FROM visitors WHERE checkout_time IS NULL");
?>

<div class="container mt-4 my-5 pt-2 position-relative">
    <h3 class="text-center py-4">Check-Out Visitor</h3>
    <div class="position-relative" style="height: 40px;">
    <?php if (isset($_GET['success'])): ?>
        <div id="successAlert" class="alert alert-success alert-dismissible fade show top-20 start-50 translate-middle-x" role="alert" style="z-index: 1050;">
            Visitor telah berhasil check-out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success_all'])): ?>
        <div id="successAlertAll" class="alert alert-success alert-dismissible fade show top-20 start-50 translate-middle-x" role="alert" style="z-index: 1050;">
            Semua visitor telah berhasil check-out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    </div>

    <div class="container my-4">
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmCheckoutAll">
                Check-Out All
            </button>
        </div>
        
        <table id="visitorTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Check-in Time</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $visitors->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= date('d F Y H:i', strtotime($row['checkin_time'])) ?></td>
                        <td class="text-center">
                            <a href="?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Check-Out</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Konfirmasi Check-Out Semua -->
<div class="modal fade" id="confirmCheckoutAll" tabindex="-1" aria-labelledby="confirmCheckoutAllLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable d-flex align-items-center justify-content-center">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title w-100" id="confirmCheckoutAllLabel">Konfirmasi Check-Out Semua</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column">
        <p class="fs-5">Apakah Anda yakin ingin check-out semua visitor?</p>
      </div>
      <div class="modal-footer d-flex">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="?checkout_all=true" class="btn btn-danger">Ya, Check-Out Semua</a>
      </div>
    </div>
  </div>
</div>  

<!-- DataTables JS + CSS -->
<script>
    $(document).ready(function() {
        $('#visitorTable').DataTable({
            responsive: true,
            lengthMenu: [
                [5, 10, 25, -1],
                [5, 10, 25, "All"]
            ],
            "pageLength": -1,
            "order": [[1, "desc"]],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Tidak ada data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(dari _MAX_ total entri)"
            }
        });
        $('#visitorTable_filter input').attr('placeholder', 'Search visitors...');

        // Hapus alert otomatis setelah 2 detik
        setTimeout(() => {
            let successAlert = document.getElementById('successAlert');
            if (successAlert) {
                let bsAlert = new bootstrap.Alert(successAlert);
                bsAlert.close();
            }
            let successAlertAll = document.getElementById('successAlertAll');
            if (successAlertAll) {
                let bsAlert = new bootstrap.Alert(successAlertAll);
                bsAlert.close();
            }
        }, 2000);
    });
</script>
<?php include '../includes/footer.php'; ?>
<?php ob_end_flush(); ?>
