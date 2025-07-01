    <?php
    include '../includes/auth.php';
    include '../includes/config.php';
    include '../includes/header.php';

    $history = $conn->query("SELECT * FROM visitors");
    ?>

    <!-- Tambahkan CSS DataTables dan Bootstrap Icons -->
    <div class="container mt-4 my-5 pt-2">
        <h3 class="text-center py-4">Visitor History</h3>
        <div class="container">
            <!-- Search Box Custom -->
            <div id="customSearch" class="mb-3"></div>

            <!-- Tombol Download & Filter -->
            <!-- Tombol Filter -->
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <a href="export_history.php" id="downloadBtn" class="btn btn-primary">
                    <i class="bi bi-download"></i> Download History
                </a>

                <!-- Trigger Modal -->
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#dateFilterModal">
                    <i class="bi bi-calendar"></i> Filter Tanggal
                </button>
            </div>

            <!-- Modal Filter Tanggal -->
            <div class="modal fade" id="dateFilterModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="dateFilterModalLabel">Filter Berdasarkan Tanggal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">Mulai Tanggal</label>
                                <input type="date" id="startDate" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="endDate" class="form-label">Sampai Tanggal</label>
                                <input type="date" id="endDate" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="resetFilterBtn">Reset</button>
                            <button class="btn btn-success" onclick="applyDateFilter()">Terapkan</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Tambahkan Kolom Aksi di Tabel -->
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="visitorTable" class="table table-bordered table-striped" style="min-width: 900px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>No.Telepon</th>
                            <th>Kegiatan</th>
                            <th>Perusahaan</th>
                            <th>PIC</th>
                            <th>Ticket</th>
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
                                <td><?= $row['tanggal'] ?></td>
                                <td><?= $row['NoTelepon'] ?></td>
                                <td><?= $row['Kegiatan'] ?></td>
                                <td><?= $row['Perusahaan'] ?></td>
                                <td><?= $row['PIC'] ?></td>
                                <td><?= $row['Ticket'] ?></td>
                                <td><?= !empty($row['checkin_time']) ? date('H:i', strtotime($row['checkin_time'])) : '' ?></td>
                                <td><?= !empty($row['checkout_time']) ? date('H:i', strtotime($row['checkout_time'])) : '' ?></td>

                                <td class="action-buttons">
                                    <button class="btn btn-info btn-sm viewDetail"
                                        data-name="<?= $row['name'] ?>"
                                        data-tanggal="<?= $row['tanggal'] ?>"
                                        data-telepon="<?= $row['NoTelepon'] ?>"
                                        data-kegiatan="<?= $row['Kegiatan'] ?>"
                                        data-perusahaan="<?= $row['Perusahaan'] ?>"
                                        data-pic="<?= $row['PIC'] ?>"
                                        data-ticket="<?= $row['Ticket'] ?>"
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
                                        data-tanggal="<?= $row['tanggal'] ?>"
                                        data-telepon="<?= $row['NoTelepon'] ?>"
                                        data-kegiatan="<?= $row['Kegiatan'] ?>"
                                        data-perusahaan="<?= $row['Perusahaan'] ?>"
                                        data-ticket="<?= $row['Ticket'] ?>"
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
            <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel">
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
                                    <div class="col-4 fw-bold text-secondary">tanggal:</div>
                                    <div class="col-8 fw-bold" id="modaltanggal"></div>
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
                                    <div class="col-4 fw-bold text-secondary">PIC:</div>
                                    <div class="col-8 fw-bold" id="modalPIC"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Ticket</div>
                                    <div class="col-8 fw-bold" id="modalticket"></div>
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
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
                <div class="modal-dialog modal-dialog-scrollable d-flex align-items-center justify-content-center" role="document">
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
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
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
                                <div class="mb-3">
                                    <label class="form-label">Ticket</label>
                                    <input type="text" class="form-control" name="Ticket" id="editTicket">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Waktu Check-In</label>
                                    <input type="datetime-local" class="form-control" name="checkin_time" id="editCheckin">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Waktu Check-Out</label>
                                    <input type="datetime-local" class="form-control" name="checkout_time" id="editCheckout">
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



        <!-- DataTables-->
        <script>
            $(document).ready(function() {
                $('.viewDetail').on('click', function() {
                    $('#modalName').text($(this).data('name'));
                    $('#modaltanggal').text($(this).data('tanggal'));
                    $('#modalTelepon').text($(this).data('telepon'));
                    $('#modalKegiatan').text($(this).data('kegiatan'));
                    $('#modalPerusahaan').text($(this).data('perusahaan'));
                    $('#modalPIC').text($(this).data('pic'));
                    $('#modalticket').text($(this).data('ticket'));

                    // Format jam:menit saja
                    $('#modalCheckin').text(formatTime($(this).data('checkin')));
                    $('#modalCheckout').text(formatTime($(this).data('checkout')));

                    // Gambar
                    $('#modalFotoDiri').attr('src', window.location.origin + '/Visitor-web/' + $(this).data('foto_diri'));
                    $('#modalFotoKTP').attr('src', window.location.origin + '/Visitor-web/' + $(this).data('foto_ktp'));
                });
            });

            function formatTime(datetime) {
                if (!datetime) return '-';

                var splitDateTime = datetime.split(' '); // pisahkan tanggal dan waktu
                if (splitDateTime.length < 2) return '-';

                var time = splitDateTime[1]; // ambil bagian waktu (jam:menit:detik)
                var timeParts = time.split(':');
                if (timeParts.length < 2) return '-';

                var hours = timeParts[0];
                var minutes = timeParts[1];

                return hours + ':' + minutes; // hanya jam:menit
            }
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
                function formatDateTimeLocal(datetimeStr) {
                    if (!datetimeStr) return '';

                    const date = new Date(datetimeStr.replace(' ', 'T'));
                    const offset = date.getTimezoneOffset();
                    const localDate = new Date(date.getTime() - (offset * 60000)); // UTC ke lokal

                    return localDate.toISOString().slice(0, 16); // hasilnya: "2025-05-19T21:21"
                }


                $('.editVisitor').on('click', function() {
                    $('#editId').val($(this).data('id'));
                    $('#editName').val($(this).data('name'));
                    $('#editTelepon').val($(this).data('telepon'));
                    $('#editKegiatan').val($(this).data('kegiatan'));
                    $('#editPerusahaan').val($(this).data('perusahaan'));
                    $('#editTicket').val($(this).data('ticket'));

                    // Format datetime for input
                    const checkin = formatDateTimeLocal($(this).data('checkin'));
                    const checkout = formatDateTimeLocal($(this).data('checkout'));

                    $('#editCheckin').val(checkin);
                    $('#editCheckout').val(checkout);
                });


                // Delete Visitor
                $('.deleteVisitor').on('click', function() {
                    $('#deleteId').val($(this).data('id'));
                    $('#deleteName').text($(this).data('name'));
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                const table = $('#visitorTable').DataTable();

                // Fungsi Terapkan Filter
                window.applyDateFilter = function() {
                    const start = $('#startDate').val();
                    const end = $('#endDate').val();

                    if (start && end) {
                        const formattedStartDate = start; // Format: YYYY-MM-DD
                        const formattedEndDate = end;

                        $.fn.dataTable.ext.search = [];
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            const tanggal = data[2]; // Kolom "Tanggal" ada di index ke-2
                            if (!tanggal) return false;

                            return tanggal >= formattedStartDate && tanggal <= formattedEndDate;
                        });

                        table.draw();

                        const downloadLink = `export_history.php?startDate=${formattedStartDate}&endDate=${formattedEndDate}`;
                        $('#downloadBtn').attr('href', downloadLink);

                        $('#dateFilterModal').modal('hide');
                        removeModalBackdrop();
                    } else {
                        alert("Mohon pilih tanggal mulai dan akhir.");
                    }
                };

                // Reset Filter
                $('#resetFilterBtn').on('click', function() {
                    $('#startDate').val(''); // Menghapus nilai tanggal mulai
                    $('#endDate').val(''); // Menghapus nilai tanggal akhir
                    $.fn.dataTable.ext.search = [];
                    table.draw();

                    // Update URL untuk menghapus parameter startDate dan endDate di link download
                    const downloadLink = 'export_history.php';
                    $('#downloadBtn').attr('href', downloadLink);
                    $('#dateFilterModal').modal('hide');
                    removeModalBackdrop();
                });

                // Fungsi untuk menghapus backdrop modal
                function removeModalBackdrop() {
                    $('.modal-backdrop').remove();
                }
            });
        </script>