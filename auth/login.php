<?php
session_start();
include '../includes/config.php';
include '../includes/header.php';

// Jika pengguna sudah login, alihkan ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: ../pages/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Gunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // "s" berarti string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'username' => $user['username']
        ];
        $_SESSION['LAST_ACTIVITY'] = time();
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>

<!-- FORM LOGIN -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow p-5" style="width: 100%; max-width: 600px;">
        <h3 class="text-center mb-4">Selamat Datang!</h3>

        <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>

        <form method="post">
            <div class="mb-3">
                <input type="text" name="username" placeholder="&#xf007; Username" required class="form-control rounded-pill" style="font-family: 'FontAwesome', 'Arial';">
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="&#xf023; Password" required class="form-control rounded-pill" style="font-family: 'FontAwesome', 'Arial';">
            </div>
            <button type="submit" class="btn btn-primary rounded-pill w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <!-- Garis horizontal dengan tulisan dan tombol kembali -->
        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1">
            <span class="px-2 text-muted">atau</span>
            <hr class="flex-grow-1">
        </div>

        <a href="/Visitor-web/pages/dashboard.php" class="btn btn-outline-secondary rounded-pill w-100">
            <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
