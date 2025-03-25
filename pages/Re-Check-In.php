<?php
ob_start();
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';

// Ambil daftar nama pengunjung
$query = "SELECT id, name FROM visitors";
$result = mysqli_query($conn, $query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visitor_id = $_POST['visitor_id'] ?? '';
    $kegiatan_baru = $_POST['kegiatan'] ?? '';

    if (!empty($visitor_id) && !empty($kegiatan_baru)) {
        // Ambil data pengunjung berdasarkan ID
        $stmt = $conn->prepare("SELECT name, NoTelepon, Perusahaan, foto_diri, foto_ktp FROM visitors WHERE id = ?");
        $stmt->bind_param("i", $visitor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $stmt = $conn->prepare("INSERT INTO visitors (name, NoTelepon, Kegiatan, Perusahaan, foto_diri, foto_ktp) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $row['name'], $row['NoTelepon'], $kegiatan_baru, $row['Perusahaan'], $row['foto_diri'], $row['foto_ktp']);

            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit;
            } else {
                $alert = '<div class="alert alert-danger">❌ Terjadi kesalahan: ' . htmlspecialchars($stmt->error) . '</div>';
            }
        } else {
            $alert = '<div class="alert alert-warning">⚠️ Data pengunjung tidak ditemukan!</div>';
        }
    } else {
        $alert = '<div class="alert alert-warning">⚠️ Harap lengkapi semua kolom!</div>';
    }
}

$result = $conn->query("SELECT id, name FROM visitors"); // Pastikan query ini berjalan

if (isset($_GET['success'])) {
    $alert = '<div class="alert alert-success alert-dismissible fade show position-relative" id="successAlert" role="alert">
                ✅ Kegiatan berhasil ditambahkan!
                <button type="button" class="btn-close position-absolute top-0 end-0" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
}

?>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card-header text-black text-center">
            <h2>Re-Checkin Visitors</h2>
        </div>
        <div class="position-relative" style="height: 40px;">
            <?php if (isset($alert)) echo $alert; ?>
        </div>
        <div class="card shadow-lg mb-5 mt-4 p-4">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Pilih Nama Pengunjung:</label>
                        <select class="form-select" name="visitor_id" id="visitorSelect" required>
                            <option value="">-- Pilih Nama --</option>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <option value="<?= htmlspecialchars($row['id']); ?>">
                                    <?= htmlspecialchars($row['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kegiatan Baru:</label>
                        <input type="text" class="form-control" name="kegiatan" required placeholder="Masukkan kegiatan baru">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Tambah Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let alertElement = document.getElementById("successAlert");
        if (alertElement) {
            setTimeout(function() {
                alertElement.style.opacity = "0";
                setTimeout(() => alertElement.style.display = "none", 500);
            }, 2000);
        }
    });

    $(document).ready(function() {
        $('#visitorSelect').select2({
            placeholder: "-- Pilih Nama --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
</html>
<?php ob_end_flush();