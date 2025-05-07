<?php
ob_start();
session_start();
include '../includes/config.php';
include '../includes/header.php';

// Jika pengguna sudah login, alihkan ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: ../pages/dashboard.php");
    exit;
}

function loginWithLDAP($username, $password)
{
    $ldap_server = "IEB-JKTDC02-DEV.ieb.go.id";
    $domain = "ieb";
    $ldap_user = $domain . "\\" . $username;

    $ldap_conn = ldap_connect($ldap_server);

    if (!$ldap_conn) {
        return false;
    }

    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

    $bind = @ldap_bind($ldap_conn, $ldap_user, $password);

    if ($bind) {
        // Ambil informasi user terlebih dahulu
        $user_search = ldap_search($ldap_conn, "dc=ieb,dc=go,dc=id", "(sAMAccountName=$username)");
        $user_entries = ldap_get_entries($ldap_conn, $user_search);

        if ($user_entries['count'] > 0) {
            $user_dn = $user_entries[0]['dn'];
            $_SESSION['displayName'] = $user_entries[0]['cn'][0]; // Simpan CN ke session

            // Cek apakah user adalah anggota dari grup di OU=Security Group
            $group_search = ldap_search($ldap_conn, "OU=Security Group,dc=ieb,dc=go,dc=id", 
                "(member=$user_dn)");
            $group_entries = ldap_get_entries($ldap_conn, $group_search);

            if ($group_entries['count'] > 0) {
                ldap_unbind($ldap_conn);
                return true;
            }
        }
    }

    ldap_unbind($ldap_conn);
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Cek ke database dulu
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        // Login sukses dari DB
        $_SESSION['user'] = ['username' => $user['username']];
        $_SESSION['LAST_ACTIVITY'] = time();
        header("Location: ../pages/dashboard.php");
        exit;
    } elseif (loginWithLDAP($username, $password)) {
        // Login sukses dari LDAP
        $_SESSION['user'] = ['username' => $username];
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
                <input type="text" name="username" placeholder="Username" required class="form-control rounded-pill" style="font-family: 'FontAwesome', 'Arial';">
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password" required class="form-control rounded-pill" style="font-family: 'FontAwesome', 'Arial';">
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