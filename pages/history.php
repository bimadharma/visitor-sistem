    <?php
    include '../includes/auth.php';
    include '../includes/config.php';
    include '../includes/header.php';

    $history = $conn->query("SELECT * FROM visitors");
    ?>

    <!-- Tambahkan CSS DataTables dan Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <div class="container mt-4 my-5 pt-2">
        <h3 class="text-center py-4">Visitor History</h3>
        <div class="container">
            <!-- Search Box Custom -->
            <div id="customSearch" class="mb-3"></div>

            <!-- Tombol Download & Filter -->
            <div class="d-flex justify-content-between mb-3">
                <a href="export_history.php" class="btn btn-primary">
                    <i class="bi bi-download"></i> Download History
                </a>

            </div>

            <!-- Tambahkan Kolom Aksi di Tabel -->
             <div class="table-responsive">
            <table id="visitorTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>No.Telepon</th>
                        <th>Kegiatan</th>
                        <th>Perusahaan</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['NoTelepon'] ?></td>
                            <td><?= $row['Kegiatan'] ?></td>
                            <td><?= $row['Perusahaan'] ?></td>
                            <td><?= !empty($row['checkin_time']) ? date('d F Y H:i', strtotime($row['checkin_time'])) : '' ?></td>
                            <td><?= !empty($row['checkout_time']) ? date('d F Y H:i', strtotime($row['checkout_time'])) : '' ?></td>

                            <td>
                                <button class="btn btn-info btn-sm viewDetail"
                                    data-name="<?= $row['name'] ?>"
                                    data-telepon="<?= $row['NoTelepon'] ?>"
                                    data-kegiatan="<?= $row['Kegiatan'] ?>"
                                    data-perusahaan="<?= $row['Perusahaan'] ?>"
                                    data-foto_diri="<?= $row['foto_diri'] ?>"
                                    data-foto_ktp="<?= $row['foto_ktp'] ?>"
                                    data-checkin="<?= $row['checkin_time'] ?>"
                                    data-checkout="<?= $row['checkout_time'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#viewModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm editVisitor"
                                    data-id="<?= $row['id'] ?>"
                                    data-name="<?= $row['name'] ?>"
                                    data-telepon="<?= $row['NoTelepon'] ?>"
                                    data-kegiatan="<?= $row['Kegiatan'] ?>"
                                    data-perusahaan="<?= $row['Perusahaan'] ?>"
                                    data-checkin="<?= $row['checkin_time'] ?>"
                                    data-checkout="<?= $row['checkout_time'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button class="btn btn-danger btn-sm deleteVisitor"
                                    data-id="<?= $row['id'] ?>"
                                    data-name="<?= $row['name'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>

            <!-- Modal View Detail -->
            <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-black text-white">
                            <h5 class="modal-title" id="viewModalLabel">Detail Visitor</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container p-4">
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Nama:</div>
                                    <div class="col-8 fw-bold" id="modalName"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">No.Telepon:</div>
                                    <div class="col-8 fw-bold" id="modalTelepon"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Kegiatan:</div>
                                    <div class="col-8 fw-bold" id="modalKegiatan"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Perusahaan:</div>
                                    <div class="col-8 fw-bold" id="modalPerusahaan"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Check-in:</div>
                                    <div class="col-8 fw-bold" id="modalCheckin"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Check-out:</div>
                                    <div class="col-8 fw-bolder" id="modalCheckout"></div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-6 text-center">
                                        <p class="fw-bold text-secondary">Foto Diri</p>
                                        <img id="modalFotoDiri" src="" class="img-fluid border rounded shadow" alt="Foto Diri">
                                    </div>
                                    <div class="col-6 text-center">
                                        <p class="fw-bold text-secondary">Foto KTP</p>
                                        <img id="modalFotoKTP" src="" class="img-fluid border rounded shadow" alt="Foto KTP">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus -->
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="delete_visitor.php" method="POST">
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus visitor <strong id="deleteName"></strong>?</p>
                                <input type="hidden" name="id" id="deleteId">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Modal Edit -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="editModalLabel">Edit Visitor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="update_visitor.php" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="editId">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" name="name" id="editName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" name="NoTelepon" id="editTelepon" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kegiatan</label>
                                    <input type="text" class="form-control" name="Kegiatan" id="editKegiatan" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Perusahaan</label>
                                    <input type="text" class="form-control" name="Perusahaan" id="editPerusahaan" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- DataTables JS + CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.viewDetail').on('click', function() {
                    $('#modalName').text($(this).data('name'));
                    $('#modalTelepon').text($(this).data('telepon'));
                    $('#modalKegiatan').text($(this).data('kegiatan'));
                    $('#modalPerusahaan').text($(this).data('perusahaan'));
                    $('#modalCheckin').text($(this).data('checkin'));
                    $('#modalCheckout').text($(this).data('checkout'));

                    // Menampilkan gambar
                    $('#modalFotoDiri').attr('src', window.location.origin + '/Visitor-web/' + $(this).data('foto_diri'));
                    $('#modalFotoKTP').attr('src', window.location.origin + '/Visitor-web/' + $(this).data('foto_ktp'));


                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#visitorTable').DataTable({
                    responsive: true,
                    lengthMenu: [
                        [5, 10, 25, -1],
                        [5, 10, 25, "All"]
                    ],
                    "pageLength": -1,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ entri",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        },
                        zeroRecords: "Tidak ada data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                        infoFiltered: "(disaring dari _MAX_ total entri)"
                    }
                });
                $('#visitorTable_filter input').attr('placeholder', 'Search visitors...');

            });
        </script>

        <script>
            $(document).ready(function() {
                // Edit Visitor
                $('.editVisitor').on('click', function() {
                    $('#editId').val($(this).data('id'));
                    $('#editName').val($(this).data('name'));
                    $('#editTelepon').val($(this).data('telepon'));
                    $('#editKegiatan').val($(this).data('kegiatan'));
                    $('#editPerusahaan').val($(this).data('perusahaan'));
                });

                // Delete Visitor
                $('.deleteVisitor').on('click', function() {
                    $('#deleteId').val($(this).data('id'));
                    $('#deleteName').text($(this).data('name'));
                });
            });
        </script>