<?php
ob_start();
include '../includes/auth.php';
include '../includes/config.php';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Tangkap data input
    $name = $_POST['name'];
    $NoTelepon = $_POST['NoTelepon'];
    $Kegiatan = $_POST['Kegiatan'];
    $Perusahaan = $_POST['Perusahaan'];
    $PIC = $_POST['PIC'];
    $Ticket = $_POST['Ticket'];

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

    $ktpPath = null;
    // ====== FOTO KTP (File Upload atau Base64 Kamera) ======
    $ktpPath = null;
    $folderKTP = __DIR__ . '/../assets/ktp/';
    if (!file_exists($folderKTP)) {
        mkdir($folderKTP, 0777, true);
    }

    // Jika pakai upload file biasa
    if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] == 0) {
        $fileKTP = $_FILES['foto_ktp'];
        $ktpName = uniqid('ktp_', true) . '_' . basename($fileKTP['name']);
        $ktpPath = 'assets/ktp/' . $ktpName; // Path untuk disimpan di DB
        move_uploaded_file($fileKTP['tmp_name'], $folderKTP . $ktpName);
    }
    // Jika pakai kamera (base64)
    elseif (!empty($_POST['foto_ktp_base64'])) {
        $base64Image = $_POST['foto_ktp_base64'];

        // Ambil bagian data:image/png;base64,....
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, jpeg

            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                die('Format gambar tidak didukung.');
            }

            $base64Image = base64_decode($base64Image);
            if ($base64Image === false) {
                die('Gagal decode gambar.');
            }

            $ktpName = uniqid('ktp_', true) . '.' . $type;
            file_put_contents($folderKTP . $ktpName, $base64Image);
            $ktpPath = 'assets/ktp/' . $ktpName; // Path untuk disimpan di DB
        }
    }


    $name = trim(strtolower($name));
    $NoTelepon = trim($NoTelepon);

    // ====== CEK DUPLIKAT NAMA ATAU NOMOR ======
    $checkQuery = "SELECT * FROM visitors 
                   WHERE LOWER(TRIM(name)) = '$name' 
                   OR TRIM(NoTelepon) = '$NoTelepon'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo "
        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Nama atau Nomor Telepon sudah pernah digunakan. Silakan gunakan data lain.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } else {
        // ====== INSERT DATA BARU ======
        $query = "INSERT INTO visitors (name, NoTelepon, Kegiatan, Perusahaan, PIC, Ticket, foto_diri, foto_ktp) 
                  VALUES ('$name', '$NoTelepon', '$Kegiatan', '$Perusahaan', '$PIC','$Ticket', '$fotoDiriPath', '$ktpPath')";

        if ($conn->query($query) === TRUE) {
            header("Location: registration.php?success=1");
            exit();
        } else {
            echo "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Gagal Check-In: " . $conn->error . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
    }
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

        #ktpPreview {
            width: 100%;
            /* Gambar mengisi lebar kontainer */
            height: 100%;
            /* Gambar mengisi tinggi kontainer */
            object-fit: cover;
            /* Menjaga proporsi gambar dan mengisi seluruh area */
            position: absolute;
            /* Posisi absolut di dalam kontainer */
            top: 0;
            left: 0;
            z-index: -1;
            /* Membuat gambar berada di belakang teks dan ikon */
        }
    </style>

</head>
<div class="container mt-4 my-5 pt-2">
    <h3 class="text-center py-4">New Registration Visitor</h3>
    <div class="position-relative" style="height: 40px;">
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<div class='alert alert-success alert-dismissible fade show top-20 start-50 translate-middle-x' role='alert' id='autoCloseAlert'>
            Visitor berhasil Check-In!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        }
        ?>
    </div>
    <form method="post" enctype="multipart/form-data" class="my-4">
        <!-- Input Nama -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-person-fill"></i></span>
            <input type="text" name="name" placeholder="Nama Lengkap" required class="form-control">
        </div>

        <!-- Input No Telepon -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-telephone-fill"></i></span>
            <input type="number" name="NoTelepon" placeholder="No Telepon" required class="form-control">
        </div>

        <!-- Input Kegiatan -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-briefcase-fill"></i></span>
            <input type="text" name="Kegiatan" placeholder="Kegiatan" required class="form-control">
        </div>

        <!-- Input Perusahaan -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-building"></i></span>
            <input type="text" name="Perusahaan" placeholder="Perusahaan" required class="form-control">
        </div>

        <!-- Input PIC (displayName) -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-person-circle"></i></span>
            <?php
            // Ambil displayName dari session yang telah disimpan
            $displayName = isset($_SESSION['displayName']) ? $_SESSION['displayName'] : "Nama PIC tidak tersedia";
            ?>
            <input type="text" name="PIC" value="<?php echo htmlspecialchars($displayName); ?>" placeholder="PIC (Nama Anda)" readonly class="form-control bg-light text-muted" style="background-color: #d3d3d3 !important;">
        </div>
        <!-- Input Ticket -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-ticket-fill"></i></span>
            <input type="number" name="Ticket" placeholder="Nomor Tiket Helpdesk" required class="form-control">
        </div>
        <div class="row">
            <!-- KTP -->
            <div class="col-md-6 mb-4 text-center">
                <label class="form-label fw-bold">Foto KTP</label>
                <div id="ktpContainer" class="border border-2 border-dark position-relative mx-auto rounded-3 overflow-hidden p-2" style="width:300px; height:auto;">
                    <!-- Label Upload + Input File -->
                    <div id="ktpUploadSection" class="w-100">
                        <div class="position-relative w-100" style="height: 185px;">
                            <input type="file" name="foto_ktp" id="fotoKTP" accept="image/*" class="form-control position-absolute w-100 h-100 opacity-0">
                            <label for="fotoKTP" id="labelKTP" class="position-absolute w-100 h-100 d-flex flex-column justify-content-center align-items-center m-0" style="cursor: pointer;">
                                <i class="bi bi-upload fs-1 text-dark"></i>
                                <p class="text-muted">Klik untuk Upload KTP</p>
                            </label>
                            <img id="ktpPreview" src="" alt="Hasil Foto KTP" class="position-absolute w-100 h-100 object-fit-cover d-none">
                        </div>
                        <button type="button" id="ktpCameraBtn" class="btn btn-sm btn-secondary my-2">
                            <i class="bi bi-camera-fill"></i> Gunakan Kamera
                        </button>
                    </div>

                    <!-- Mode Kamera -->
                    <div id="ktpCameraSection" class="d-none">
                        <p class="text-muted mb-1">Mode Kamera</p>
                        <video id="ktpVideo" width="100%" height="225" autoplay muted class="border rounded-3"></video>
                        <img id="ktpPreviewFoto" src="" alt="Hasil Foto KTP" class="border rounded-3 mt-2 d-none" width="280" height="215">
                        <canvas id="ktpCanvas" width="300" height="225" class="d-none"></canvas>
                        <button type="button" id="ktpCaptureBtn" class="btn btn-success mt-2 w-100">Ambil Foto Sekarang</button>
                        <button type="button" id="ktpRetakeBtn" class="btn bg-warning mt-2 w-100 d-none">Ulang Foto</button>
                        <button type="button" id="ktpBackBtn" class="btn btn-outline-dark mt-2 w-100">Kembali ke Upload</button>
                    </div>
                    <input type="hidden" name="foto_ktp_base64" id="fotoKTPBase64">
                </div>
            </div>

            <!-- Foto Diri -->
            <div class="col-md-6 mb-4 text-center">
                <label class="form-label fw-bold">Foto Diri Anda</label>
                <div id="diriContainer" class="border border-2 border-dark position-relative mx-auto rounded-3 overflow-hidden p-2" style="width:300px; height:auto;">
                    <div id="diriCaptureSection" style="height: 225px; cursor: pointer;" class="position-relative w-100">
                        <div class="position-absolute w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-camera-fill fs-1 text-dark"></i>
                            <p class="text-muted">Klik Kamera untuk Ambil Foto</p>
                        </div>
                    </div>
                    <video id="diriVideo" width="100%" height="225" autoplay muted class="border rounded-3 mt-2 d-none"></video>
                    <canvas id="diriCanvas" width="300" height="225" class="d-none"></canvas>
                    <img id="diriPreview" src="" alt="Hasil Foto Diri" class="border rounded-3 mt-2 d-none" width="280" height="215">
                    <button type="button" id="diriCaptureBtn" class="btn btn-success mt-2 w-100 d-none">Ambil Foto Sekarang</button>
                    <button type="button" id="diriRetakeBtn" class="btn bg-warning mt-2 w-100 d-none">Ulang Foto</button>
                    <input type="hidden" name="foto_diri_base64" id="fotoDiriBase64">
                </div>
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="text-center">
            <button type="submit" name="submit" class="btn btn-success w-50 py-3 my-2 rounded-pill">Check-In</button>
        </div>
    </form>
</div>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        let ktpStream = null;
        let diriStream = null;

        const ktpRetakeBtn = document.getElementById("ktpRetakeBtn");
        const fotoKTP = document.getElementById("fotoKTP");
        const labelKTP = document.getElementById("labelKTP");
        const ktpVideo = document.getElementById("ktpVideo");
        const ktpCanvas = document.getElementById("ktpCanvas");
        const ktpPreview = document.getElementById("ktpPreview");
        const ktpPreviewFoto = document.getElementById("ktpPreviewFoto");
        const fotoKTPBase64 = document.getElementById("fotoKTPBase64");
        const ktpCameraBtn = document.getElementById("ktpCameraBtn");
        const ktpCaptureBtn = document.getElementById("ktpCaptureBtn");
        const ktpBackBtn = document.getElementById("ktpBackBtn");
        const ktpUploadSection = document.getElementById("ktpUploadSection");
        const ktpCameraSection = document.getElementById("ktpCameraSection");

        const diriVideo = document.getElementById("diriVideo");
        const diriCanvas = document.getElementById("diriCanvas");
        const diriPreview = document.getElementById("diriPreview");
        const fotoDiriBase64 = document.getElementById("fotoDiriBase64");
        const diriCaptureSection = document.getElementById("diriCaptureSection");
        const diriCaptureBtn = document.getElementById("diriCaptureBtn");
        const diriRetakeBtn = document.getElementById("diriRetakeBtn");

        // ========= KTP =========
        fotoKTP.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    ktpPreview.src = event.target.result;
                    ktpPreview.classList.remove("d-none");
                    fotoKTPBase64.value = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        ktpCameraBtn.addEventListener("click", async () => {
            // Hapus hasil upload jika sebelumnya sudah upload
            ktpPreview.src = "";
            ktpPreview.classList.add("d-none");
            fotoKTPBase64.value = "";
            fotoKTP.value = ""; // reset input file

            if (ktpStream) stopStream(ktpStream);
            try {
                ktpStream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                ktpVideo.srcObject = ktpStream;
                ktpUploadSection.classList.add("d-none");
                ktpCameraSection.classList.remove("d-none");
            } catch (err) {
                alert("Tidak dapat mengakses kamera.");
            }
        });

        let sudahAmbilKTP = false;

        ktpCaptureBtn.addEventListener("click", async function() {
            if (!sudahAmbilKTP) {
                ktpCanvas.getContext('2d').drawImage(ktpVideo, 0, 0, 300, 225);
                const imageData = ktpCanvas.toDataURL("image/png");

                ktpPreviewFoto.src = imageData;
                ktpPreviewFoto.classList.remove("d-none");
                fotoKTPBase64.value = imageData;

                ktpVideo.classList.add("d-none");
                ktpCaptureBtn.innerText = "Ulang Foto";
                ktpCaptureBtn.classList.replace("btn-success", "btn-warning");

                sudahAmbilKTP = true;
                stopStream(ktpStream);
            } else {
                try {
                    ktpStream = await navigator.mediaDevices.getUserMedia({
                        video: true
                    });
                    ktpVideo.srcObject = ktpStream;

                    ktpPreviewFoto.classList.add("d-none");
                    ktpVideo.classList.remove("d-none");

                    ktpCaptureBtn.innerText = "Ambil Foto Sekarang";
                    ktpCaptureBtn.classList.replace("btn-warning", "btn-success");

                    sudahAmbilKTP = false;
                } catch (err) {
                    alert("Tidak dapat mengakses kamera.");
                }
            }
        });



        ktpRetakeBtn.addEventListener("click", async () => {
            if (ktpStream) stopStream(ktpStream);
            try {
                ktpStream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                ktpVideo.srcObject = ktpStream;
                ktpVideo.classList.remove("d-none");
                ktpPreviewFoto.classList.add("d-none");
                ktpCaptureBtn.classList.remove("d-none");
                ktpRetakeBtn.classList.add("d-none");
            } catch (err) {
                alert("Tidak dapat mengakses kamera.");
            }
        });


        ktpBackBtn.addEventListener("click", () => {
            stopStream(ktpStream);

            // Hapus hasil kamera jika sebelumnya pakai kamera
            ktpPreviewFoto.src = "";
            ktpPreviewFoto.classList.add("d-none");
            fotoKTPBase64.value = "";
            ktpCaptureBtn.innerText = "Ambil Foto Sekarang";
            ktpCaptureBtn.classList.replace("btn-warning", "btn-success");
            sudahAmbilKTP = false;

            ktpCameraSection.classList.add("d-none");
            ktpUploadSection.classList.remove("d-none");
            ktpVideo.classList.remove("d-none");
        });

        // ========= foto Diri =========
        diriCaptureSection.addEventListener("click", async () => {
            if (diriStream) stopStream(diriStream);
            try {
                diriStream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                diriVideo.srcObject = diriStream;
                diriVideo.classList.remove("d-none");
                diriPreview.classList.add("d-none");
                diriCaptureSection.classList.add("d-none");
                diriCaptureBtn.classList.remove("d-none");
                diriRetakeBtn.classList.add("d-none");
            } catch (err) {
                alert("Tidak dapat mengakses kamera.");
            }
        });

        diriCaptureBtn.addEventListener("click", () => {
            diriCanvas.getContext('2d').drawImage(diriVideo, 0, 0, 300, 225);
            const imageData = diriCanvas.toDataURL("image/png");
            diriPreview.src = imageData;
            diriPreview.classList.remove("d-none");
            fotoDiriBase64.value = imageData;

            diriVideo.classList.add("d-none");
            diriCaptureBtn.classList.add("d-none");
            diriRetakeBtn.classList.remove("d-none");

            stopStream(diriStream);
        });

        diriRetakeBtn.addEventListener("click", async () => {
            if (diriStream) stopStream(diriStream);
            try {
                diriStream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                diriVideo.srcObject = diriStream;
                diriVideo.classList.remove("d-none");
                diriPreview.classList.add("d-none");
                diriCaptureBtn.classList.remove("d-none");
                diriRetakeBtn.classList.add("d-none");
            } catch (err) {
                alert("Tidak dapat mengakses kamera.");
            }
        });

        function stopStream(stream) {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }
    });
</script>
<script>
document.getElementById('fotoKTP').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    if (file && !allowedTypes.includes(file.type)) {
        alert('Hanya file gambar (jpg, jpeg, png, webp) yang diperbolehkan.');
        event.target.value = ''; // Reset input
        return;
    }

    // Preview jika valid
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('ktpPreview');
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        document.getElementById('labelKTP').classList.add('d-none');
    };
    reader.readAsDataURL(file);
});
</script>


<?php include '../includes/footer.php'; ?>
<?php ob_end_flush();
