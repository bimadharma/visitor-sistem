<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Visitor App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Visitor-web/assets/css/bootstrap.min.css">
    <style>
        .nav-link.active {
            background-color: rgba(0, 123, 255, 0.64);
            color: rgb(255, 255, 255) !important;
            font-weight: bold;
            border-radius: 50px;
            padding: 10px 12px !important;
        }

        .nav-item {
            padding: 5px;
        }

        html {
            overflow-y: scroll;
        }

        body {
            min-height: 100vh;
        }

        body.modal-open {
            padding: 0 !important;
        }
    </style>
    <link href="/Visitor-web/assets/img/logo-eximbank.png" rel="shortcut icon">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top p-3 shadow">
        <div class="container-fluid">
            <a class="navbar-brand p-2" href="/Visitor-web/pages/dashboard.php">
                <img src="/Visitor-web/assets/img/logo.png" alt="Visitor App Logo" height="40" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="/Visitor-web/pages/dashboard.php">Dashboard</a>
                    </li>
                    <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['role'])): ?>
                        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <!-- Menu khusus admin -->
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'checkin.php' ? 'active' : '' ?>" href="/Visitor-web/pages/checkin.php">Check-In</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'checkout.php' ? 'active' : '' ?>" href="/Visitor-web/pages/checkout.php">Check-Out</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'history.php' ? 'active' : '' ?>" href="/Visitor-web/pages/history.php">History</a>
                            </li>
                        <?php endif; ?>

                        <!-- Logout untuk semua user -->
                        <li class="nav-item">
                            <a href="#" class="btn btn-danger text-white mx-2 p-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>

                    <?php else: ?>
                        <!-- Jika belum login -->
                        <li class="nav-item">
                            <a class="nav-link" href="/Visitor-web/auth/login.php">Login</a>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="logoutModalLabel"><i class="bi bi-box-arrow-right me-2"></i> Konfirmasi Logout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Apakah Anda yakin ingin logout?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="/Visitor-web/auth/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>