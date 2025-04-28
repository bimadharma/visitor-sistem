<?php
session_start();
include '../includes/config.php';
include '../includes/header.php';

$today = date('Y-m-d');
$today_visitors = $conn->query("SELECT * FROM visitors WHERE DATE(checkin_time) = '$today'");
$today_count = $today_visitors->num_rows;

$still_in = $conn->query("SELECT * FROM visitors WHERE checkout_time IS NULL");
$still_in_count = $still_in->num_rows;
?>

<head>
    <style>
        .wallpaper {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            width: 100%;
            padding-top: 0.5rem;
        }

        .wallpaper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/Visitor-web/assets/img/backgroundd.jpeg') center/cover no-repeat;
            z-index: -1;
        }

        .overlay-content {
            position: relative;
            z-index: 1;
            padding: 1.5rem;
            transform: scale(0.9);
            transform-origin: top center;
        }

        .card-custom {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 1rem;
            max-height: 70vh;
            display: flex;
            flex-direction: column;
        }

        .title-heading {
            font-weight: 700;
            color: #212529;
            font-size: 1.25rem;
        }

        .table {
            table-layout: fixed;
            width: 100%;
        }

        .table th,
        .table td {
            padding: 6px 8px;
            text-align: center;
            word-wrap: break-word;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
        }

        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 20%;
        }

        .table th:nth-child(2),
        .table td:nth-child(2),
        .table th:nth-child(3),
        .table td:nth-child(3) {
            width: 40%;
        }

        .auto-scroll-wrapper {
            flex-grow: 1;
            overflow: hidden;
            position: relative;
            max-height: 300px;
        }

        .auto-scroll-inner {
            animation: scroll-vertical 20s linear infinite;
        }

        .auto-scroll-wrapper:hover .auto-scroll-inner {
            animation-play-state: paused;
        }

        .auto-scroll-wrapper.no-scroll .auto-scroll-inner {
            animation: none !important;
        }

        @keyframes scroll-vertical {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-50%);
            }
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .visitor {
            min-height: calc(100vh - 12rem);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Responsif */
        @media (max-width: 768px) {
            .overlay-content {
                padding: 1rem;
            }

            .title-heading {
                font-size: 1.1rem;
            }

            .table th,
            .table td {
                padding: 5px;
                font-size: 0.8rem;
            }

            .card-custom {
                padding: 0.75rem;
            }
        }
    </style>
</head>

<section class="wallpaper d-flex align-items-center">
    <div class="container overlay-content">
        <div class="text-center text-black mb-5">
            <h2 class="fw-bold">Visitor Management System</h2>
            <p class="lead">Pantau dan kelola kunjungan tamu Data Center secara mudah, cepat, dan aman.</p>
        </div>

        <div class="row g-4">
            <!-- Today's Visitors -->
            <div class="col-md-6">
                <div class="card card-custom h-100">
                    <h4 class="text-center mb-4 title-heading">
                        <i class="bi bi-people-fill"></i> Today's Visitors
                        <span class="badge bg-primary"><?= $today_count ?></span>
                    </h4>

                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Check-in Time</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="auto-scroll-wrapper no-scrollbar">
                        <div class="auto-scroll-inner">
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    <?php if ($today_count > 0): ?>
                                        <?php
                                        mysqli_data_seek($today_visitors, 0);
                                        while ($row = $today_visitors->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><img src="/Visitor-web/<?= htmlspecialchars($row['foto_diri']) ?>" width="50" height="50" style="object-fit: cover; border-radius: 50%;"></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= date('j F Y H:i', strtotime($row['checkin_time'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3">No visitors today.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitors Still Inside -->
            <div class="col-md-6">
                <div class="card card-custom h-100">
                    <h4 class="text-center mb-4 title-heading">
                        <i class="bi bi-person-check-fill"></i> Visitors Still Inside
                        <span class="badge bg-warning text-dark"><?= $still_in_count ?></span>
                    </h4>

                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Check-in Time</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="auto-scroll-wrapper no-scrollbar">
                        <div class="auto-scroll-inner">
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    <?php if ($still_in_count > 0): ?>
                                        <?php
                                        mysqli_data_seek($still_in, 0);
                                        while ($row = $still_in->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><img src="/Visitor-web/<?= htmlspecialchars($row['foto_diri']) ?>" width="50" height="50" style="object-fit: cover; border-radius: 50%;"></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= date('j F Y H:i', strtotime($row['checkin_time'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3">No visitors inside.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const wrappers = document.querySelectorAll('.auto-scroll-wrapper');

        wrappers.forEach(wrapper => {
            const inner = wrapper.querySelector('.auto-scroll-inner');

            wrapper.classList.remove('no-scroll');

            if (inner.scrollHeight <= wrapper.offsetHeight) {
                wrapper.classList.add('no-scroll');
            }
        });
    });
</script>

<?php include '../includes/footer.php'; ?>