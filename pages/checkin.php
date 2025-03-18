<?php
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Tangkap data input
    $name = $_POST['name'];
    $NoTelepon = $_POST['NoTelepon'];
    $Kegiatan = $_POST['Kegiatan'];
    $Perusahaan = $_POST['Perusahaan'];

    // ====== FOTO DIRI (Base64) ======
    $fotoDiriBase64 = $_POST['foto_diri_base64'];
    $fotoDiriPath = null;

    if (!empty($fotoDiriBase64)) {
        $fotoDiriBase64 = str_replace('data:image/png;base64,', '', $fotoDiriBase64);
        $fotoDiriBase64 = str_replace(' ', '+', $fotoDiriBase64);
        $fotoData = base64_decode($fotoDiriBase64);


        $folderFoto = __DIR__ . '/../assets/foto/';
        if (!file_exists($folderFoto)) {
            mkdir($folderFoto, 0777, true);
        }

        // Nama file unik
        $fotoDiriName = uniqid('foto_diri_', true) . '.png';
        $fotoDiriPath = 'assets/foto/' . $fotoDiriName;
        file_put_contents($folderFoto . $fotoDiriName, $fotoData);
    }

    // ====== FOTO KTP (File Upload) ======
    $ktpPath = null;
    if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] == 0) {
        $folderKTP = __DIR__ . '/../assets/ktp/';
        if (!file_exists($folderKTP)) {
            mkdir($folderKTP, 0777, true);
        }

        $fileKTP = $_FILES['foto_ktp'];
        $ktpName = uniqid('ktp_', true) . '_' . basename($fileKTP['name']);
        $ktpPath = 'assets/ktp/' . $ktpName; // Path untuk disimpan di DB
        move_uploaded_file($fileKTP['tmp_name'], $folderKTP . $ktpName);
    }

    // ====== INSERT DATABASE ======
    $query = "INSERT INTO visitors (name, NoTelepon, Kegiatan, Perusahaan, foto_diri, foto_ktp) 
              VALUES ('$name', '$NoTelepon', '$Kegiatan', '$Perusahaan', '$fotoDiriPath', '$ktpPath')";

    if ($conn->query($query) === TRUE) {
        header("Location: checkin.php?success=1");
        exit();
    } else {
        echo "
    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
        Gagal Check-In: " . $conn->error . "
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert' id='autoCloseAlert'>
        Visitor berhasil Check-In!
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}
?>

<head>
    <style>
        input.form-control {
            border: 1px solid rgb(0, 0, 0);
            border-radius: 12px;
            font-weight: 500;
            padding: 10px;
        }
    </style>

</head>
<h3 class="text-center py-4">Check-In Visitor</h3>
<!-- <hr class="flex-grow-1 border-3"> -->

<form method="post" enctype="multipart/form-data" class="my-5">
    <!-- Input Nama -->
    <div class="input-group mb-3">
        <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
        <input type="text" name="name" placeholder="Nama Lengkap" required class="form-control">
    </div>

    <!-- Input No Telepon -->
    <div class="input-group mb-3">
        <span class="input-group-text bg-primary text-white"><i class="bi bi-telephone-fill"></i></span>
        <input type="number" name="NoTelepon" placeholder="No Telepon" class="form-control" required>
    </div>

    <!-- Input Kegiatan -->
    <div class="input-group mb-3">
        <span class="input-group-text bg-primary text-white"><i class="bi bi-briefcase-fill"></i></span>
        <input type="text" name="Kegiatan" placeholder="Kegiatan" class="form-control" required>
    </div>

    <!-- Input Perusahaan -->
    <div class="input-group mb-3">
        <span class="input-group-text bg-primary text-white"><i class="bi bi-building"></i></span>
        <input type="text" name="Perusahaan" placeholder="Perusahaan" class="form-control" required>
    </div>

    <div class="row mb-4">
        <!-- Kolom Kiri: Foto KTP -->
        <div class="col-md-6 mb-3 text-center">
            <label class="py-3"><strong>Foto KTP</strong></label>
            <div class="border border-2 border-dark rounded-3 position-relative d-flex justify-content-center align-items-center mx-auto"
                style="cursor: pointer; width: 300px; height: 225px;">
                <div id="labelKTP" style="max-width: 100%; padding: 10px;">
                    <i class="bi bi-upload fs-1 text-dark mb-2"></i>
                    <p class="m-0 text-muted text-wrap" style="max-width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">Klik untuk Upload KTP</p>
                </div>
                <input type="file" name="foto_ktp" id="fotoKTP" accept="image/*" required
                    class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0"
                    style="cursor: pointer;">
            </div>
        </div>

        <!-- Kolom Kanan: Foto Diri Kamera -->
        <div class="col-md-6 mb-3 text-center">
            <label class="py-3"><strong>Foto Diri (Kamera Langsung)</strong></label><br>
            <video id="video" width="300" height="225" autoplay muted class="border rounded-3 mb-2 d-none"></video>
            <canvas id="canvas" width="300" height="225" class="d-none"></canvas>
            <img id="photoPreview" src="" alt="Hasil Foto" class="border rounded-3 mb-2 d-none" width="300" height="225"><br>
            <input type="hidden" name="foto_diri_base64" id="fotoDiriBase64">
            <button type="button" id="cameraBtn" class="btn btn-primary mb-2">Aktifkan Kamera</button>
        </div>
    </div>


    <!-- Tombol Submit -->
    <div class="text-center">
        <button type="submit" name="submit" class="btn btn-success w-50 py-3 my-2 rounded-pill">Check-In</button>
    </div>

</form>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const cameraBtn = document.getElementById('cameraBtn');
    const fotoDiriBase64 = document.getElementById('fotoDiriBase64');
    const photoPreview = document.getElementById('photoPreview');

    let stream = null;
    let isCameraActive = false;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });
            video.srcObject = stream;
            video.classList.remove('d-none');
            photoPreview.classList.add('d-none');
            cameraBtn.textContent = "Ambil Foto";
            isCameraActive = true;
        } catch (error) {
            alert('Tidak dapat mengakses kamera: ' + error.message);
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        video.classList.add('d-none');
        isCameraActive = false;
    }

    cameraBtn.addEventListener('click', function() {
        if (!isCameraActive) {
            startCamera();
        } else {
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');
            fotoDiriBase64.value = imageData;
            photoPreview.src = imageData;
            photoPreview.classList.remove('d-none');

            stopCamera();
            cameraBtn.textContent = "Aktifkan Kamera";
        }
    });

    //KTP verifikasi nama file
    // Menangkap input file
    const inputKTP = document.getElementById('fotoKTP');
    const labelKTP = document.getElementById('labelKTP');

    // Event saat file dipilih
    inputKTP.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            labelKTP.innerHTML = `
            <i class="bi bi-file-earmark-image fs-1 text-success mb-2"></i>
            <p class="m-0 text-dark" style="max-width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">${fileName}</p>
        `;
        }
    });

     // Auto close alert dalam 2 detik
     setTimeout(function() {
            var alert = document.getElementById('autoCloseAlert');
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 1000);
</script>



<?php include '../includes/footer.php'; ?>