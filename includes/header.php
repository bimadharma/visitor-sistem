<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor App</title>
    <link rel="stylesheet" href="/Visitor-web/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Visitor-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Visitor-web/assets/js/datatables.min.css">
    <link rel="stylesheet" href="/Visitor-web/assets/js/dataTables.bootstrap5.min.css">

    <script src="/Visitor-web/assets/js/jquery-3.7.1.min.js"></script>
    <script src="/Visitor-web/assets/js/jquery.dataTables.min.js"></script>
    <script src="/Visitor-web/assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="/Visitor-web/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/Visitor-web/assets/js/datatables.js"></script>
    <style>
        .nav-link.active {
            background-color: rgba(0, 123, 255, 0.64);
            color: rgb(255, 255, 255) !important;
            font-weight: bold;
            border-radius: 10px;
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

        .logo-navbar {
            height: 40px;
        }

        #visitorTable {
            font-size: 0.8rem;
        }

        .action-buttons .btn {
            padding: 2px 6px;
            font-size: 0.7rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .logo-navbar {
                height: 30px;
            }
        }
    </style>
    <link href="/Visitor-web/assets/img/logo-eximbank.png" rel="shortcut icon">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top p-3 shadow">
        <div class="container-fluid">
            <a class="navbar-brand" href="/Visitor-web/pages/dashboard.php">
                <img src="/Visitor-web/assets/img/logo.png" alt="Visitor App Logo" height="40" class="logo-navbar d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="/Visitor-web/pages/dashboard.php">Dashboard</a>
                    </li>

                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Menu yang ditampilkan setelah login (bisa untuk semua user) -->
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'registration.php' ? 'active' : '' ?>" href="/Visitor-web/pages/registration.php">Registration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'Re-Check-In.php' ? 'active' : '' ?>" href="/Visitor-web/pages/Re-Check-In.php">Re-Check-In</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'checkout.php' ? 'active' : '' ?>" href="/Visitor-web/pages/checkout.php">Check-Out</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'history.php' ? 'active' : '' ?>" href="/Visitor-web/pages/history.php">History</a>
                        </li>

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