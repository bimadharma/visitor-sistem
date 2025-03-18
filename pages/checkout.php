<?php
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';

// Proses Check-Out jika ada ID di URL
if (isset($_GET['id'])) {
    $conn->query("UPDATE visitors SET checkout_time=NOW() WHERE id={$_GET['id']}");
    header("Location: " . $_SERVER['PHP_SELF'] . "?success");
    exit();
}

// Ambil data visitor yang belum check-out
$visitors = $conn->query("SELECT * FROM visitors WHERE checkout_time IS NULL");
?>

<h3 class="text-center py-4">Check-Out Visitor</h3>

<div class="container my-5">
    <?php if (isset($_GET['success'])): ?>
        <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            Visitor telah berhasil check-out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <table id="visitorTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Check-in Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $visitors->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= date('d F Y H:i', strtotime($row['checkin_time'])) ?></td>
                    <td>
                        <a href="?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Check-Out</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- DataTables JS + CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('#visitorTable').DataTable({
            responsive: true,
            lengthMenu: [
                [5, 10, 25, -1],
                [5, 10, 25, "All"]
            ],
            "pageLength": -1,
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
                infoFiltered: "(disaring dari _MAX_ total entri)"
            }
        });
        $('#visitorTable_filter input').attr('placeholder', 'Search visitors...');

        // Hapus alert otomatis setelah 1 detik
        setTimeout(() => {
            let successAlert = document.getElementById('successAlert');
            if (successAlert) {
                let bsAlert = new bootstrap.Alert(successAlert);
                bsAlert.close();
            }
        }, 1000);
    });
</script>

<?php include '../includes/footer.php'; ?>