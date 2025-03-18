<?php
session_start();
include '../includes/config.php';
include '../includes/header.php';

// Pengunjung hari ini
$today = date('Y-m-d');
$today_visitors = $conn->query("SELECT * FROM visitors WHERE DATE(checkin_time) = '$today'");
$today_count = $today_visitors->num_rows; // Hitung jumlah pengunjung hari ini

// Pengunjung masih di tempat
$still_in = $conn->query("SELECT * FROM visitors WHERE checkout_time IS NULL");
$still_in_count = $still_in->num_rows; // Hitung jumlah pengunjung masih di dalam
?>

<head>
    <style>
        .margin-section {
            margin-top: 5rem;
        }

        .table-bordered th,
        .table-bordered td {
            border: 2px solid #000;
        }

.wallpaper {
    overflow: hidden; /* Mencegah gambar melampaui section */
}

.wallpaper::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('/Visitor-web/assets/img/backgroundd.jpeg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: -1; /* Letakkan di belakang konten */
}

    </style>
</head>

<section class="wallpaper">
    <div class="p-3 mb-3 margin-section text-center">
        <h3>Selamat Datang di Visitor Management System Data Center</h3>
        <h4>Pantau dan kelola kunjungan tamu Data Center secara mudah, cepat, dan aman</h4>
    </div>

    <!-- Menu Cards -->
    <div class="container my-5 pb-5">
        <div class="row justify-content-center">
            <!-- Check-in Visitor -->
            <div class="col-md-3">
                <a href="/Visitor-web/pages/checkin.php" class="text-decoration-none">
                    <div class="card text-center shadow p-3 mb-4 bg-white rounded h-100">
                        <div class="card-body">
                            <i class="bi bi-person-plus display-4 text-primary mb-3"></i>
                            <h5 class="card-title">Check-in Visitor</h5>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Check-out Visitor -->
            <div class="col-md-3">
                <a href="/Visitor-web/pages/checkout.php" class="text-decoration-none">
                    <div class="card text-center shadow p-3 mb-4 bg-white rounded h-100">
                        <div class="card-body">
                            <i class="bi bi-person-dash display-4 text-success mb-3"></i>
                            <h5 class="card-title">Check-out Visitor</h5>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Visitor History -->
            <div class="col-md-3">
                <a href="/Visitor-web/pages/history.php" class="text-decoration-none">
                    <div class="card text-center shadow p-3 mb-4 bg-white rounded h-100">
                        <div class="card-body">
                            <i class="bi bi-clock-history display-4 text-warning mb-3"></i>
                            <h5 class="card-title">Visitor History</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>



<div class="container my-5">
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <h4 class="text-center">
                <i class="bi bi-people-fill"></i> Today's Visitors
                <span class="badge bg-primary"><?= $today_count ?></span>
            </h4>
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Check-in Time</th>
                </tr>
                <?php if ($today_count > 0): ?>
                    <?php while ($row = $today_visitors->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['checkin_time'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center">No visitors today.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <h4 class="text-center">
                <i class="bi bi-person-check-fill"></i> Visitors Still Inside
                <span class="badge bg-warning text-dark"><?= $still_in_count ?></span>
            </h4>
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Check-in Time</th>
                </tr>
                <?php if ($still_in_count > 0): ?>
                    <?php while ($row = $still_in->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['checkin_time'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center">No visitors inside.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>