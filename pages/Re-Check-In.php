<?php
ob_start();
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';

// Ambil daftar nama pengunjung yang semua entri dengan nama tersebut sudah checkout, dan hanya ambil satu data tiap nama
$query = "
    SELECT MIN(id) AS id, name, MAX(Perusahaan) AS Perusahaan
    FROM visitors
    GROUP BY name
    HAVING COUNT(*) = SUM(CASE WHEN checkout_time IS NOT NULL THEN 1 ELSE 0 END)
";
$result = mysqli_query($conn, $query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visitor_id = $_POST['visitor_id'] ?? '';
    $kegiatan_baru = $_POST['kegiatan'] ?? '';
    $ticket_baru = $_POST['Ticket'] ?? '';
    $pic_ldap = $_SESSION['displayName'] ?? 'Nama PIC tidak tersedia';

    if (!empty($visitor_id) && !empty($kegiatan_baru) && !empty($ticket_baru)) {
        // Ambil data pengunjung berdasarkan ID
        $stmt = $conn->prepare("SELECT name, NoTelepon, Perusahaan, foto_diri, foto_ktp FROM visitors WHERE id = ?");
        $stmt->bind_param("i", $visitor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Masukkan data baru dengan PIC dari LDAP session dan Ticket dari form
            $stmt = $conn->prepare("INSERT INTO visitors (name, NoTelepon, Kegiatan, Perusahaan, PIC, Ticket, foto_diri, foto_ktp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssssss",
                $row['name'],
                $row['NoTelepon'],
                $kegiatan_baru,
                $row['Perusahaan'],
                $pic_ldap,
                $ticket_baru, // ← Ticket dari input form
                $row['foto_diri'],
                $row['foto_ktp']
            );

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



if (isset($_GET['success'])) {
    $alert = '<div class="alert alert-success alert-dismissible fade show position-relative" id="successAlert" role="alert">
                ✅ Kegiatan berhasil ditambahkan!
                <button type="button" class="btn-close position-absolute top-0 end-0" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
}

?>

<head>
    <!-- Select2 CSS lokal -->
    <link href="/Visitor-web/assets/js/select2.min.css" rel="stylesheet">

    <!-- jQuery lokal -->
    <script src="/Visitor-web/assets/js/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS lokal -->
    <script src="/Visitor-web/assets/js/select2.min.js"></script>

    <style>
        .select2-search__field {
            padding-left: 30px !important;
            background-image: url('/Visitor-web/assets/img/search-icon.png');
            background-size: 16px 16px;
            background-position: 10px center;
            background-repeat: no-repeat;
        }

        /* Untuk elemen hasil dari Select2 */
        .select2-container .select2-selection--single {
            padding: 8px 10px;
            height: auto;
            border-radius: 6px;
            border: 1px solid rgb(0, 0, 0);
            font-size: 1rem;
        }

        /* Untuk placeholder dan item yang sedang dipilih */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 4px;
        }

        /* Untuk ikon dropdown */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 8px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card-header text-black text-center">
            <h3>Re-Checkin Visitors</h3>
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
                            <option value="" selected disabled hidden></option>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <option
                                    value="<?= htmlspecialchars($row['id']); ?>"
                                    data-company="<?= htmlspecialchars($row['Perusahaan']); ?>">
                                    <?= htmlspecialchars($row['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perusahaan:</label>
                        <input type="text" class="form-control" id="companyInput" readonly
                            placeholder="Pilih nama terlebih dahulu"
                            style="background-color: #d3d3d3 !important; color: #495057;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kegiatan Baru:</label>
                        <input type="text" class="form-control" name="kegiatan" required placeholder="Masukkan kegiatan baru">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ticket:</label>
                        <input type="number" class="form-control" name="Ticket" required placeholder="Nomor Ticket Helpdesk">
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
            placeholder: "🔍 Mencari...",
            allowClear: true,
            width: '100%',
            dropdownParent: $(".container"),
            dropdownAutoWidth: true,
            templateResult: formatVisitor,
            templateSelection: formatVisitorSelection
        });

        function formatVisitor(visitor) {
            if (!visitor.id) {
                return visitor.text;
            }
            var $visitor = $(
                '<div style="display: flex; align-items: center;">' +
                '<img src="/Visitor-web/assets/img/user-icon.png" style="width: 20px; height: 20px; margin-right: 10px; border-radius: 50%;">' +
                '<span>' + visitor.text + '</span>' +
                '</div>'
            );
            return $visitor;
        }

        function formatVisitorSelection(visitor) {
            if (!visitor.id) {
                return visitor.text;
            }
            return $('<span><i class="fas fa-user"></i> ' + visitor.text + '</span>');
        }
    });
</script>

<script>
    $(document).ready(function () {
        $('#visitorSelect').on('change', function () {
            const selectedOption = $(this).find('option:selected');
            const company = selectedOption.data('company');
            $('#companyInput').val(company);
        });
    });
</script>
</html>
<?php ob_end_flush();
