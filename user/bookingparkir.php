<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Jakarta'); //Tambahkan ini
//amil data dari database
$query = $conn->query("
    SELECT b.booking_id, b.user_id, u.username, b.slot_id, s.lokasi, b.waktu_booking, b.waktu_mulai, b.waktu_selesai, b.status, s.jenis
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN parkir_slots s ON b.slot_id = s.id
");
//tambah booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $user_id = $_POST['user_id'];
    $slot_id = $_POST['slot_id'];
    $waktu_booking = date('Y-m-d H:i:s'); // ⬅️ gunakan waktu server
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO bookings 
        (user_id, slot_id, waktu_booking, waktu_mulai, waktu_selesai, status)
        VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([$user_id, $slot_id, $waktu_booking, $waktu_mulai, $waktu_selesai, $status]);

    header('Location: pembayaran.php?booking_id=' . $conn->lastInsertId());
    exit;
}

// Hapus booking 
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];

    // Hapus data booking berdasarkan booking_id
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);

    // Redirect agar halaman refresh dan data terupdate
    header('Location: bookingparkir.php');
    exit;
}

$query = $conn->query("
    SELECT b.booking_id, b.user_id, u.username, b.slot_id, s.lokasi, s.jenis,
           b.waktu_booking, b.waktu_mulai, b.waktu_selesai, b.status
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN parkir_slots s ON b.slot_id = s.id
    ORDER BY b.booking_id DESC
");
// $slots = $conn->query("
//     SELECT * FROM parkir_slots 
//     WHERE id NOT IN (
//         SELECT slot_id FROM bookings 
//         WHERE status IN ('pending', 'dibayar')
//     )
// ")->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->query("
    SELECT * FROM parkir_slots 
    WHERE status = 'available'
");

$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Data Booking</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
         <?php include '../includes/usersidebar.php'; ?>

        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                 <?php include '../includes/usernavbar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Booking Parkir</h1>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#modalTambahBooking">
                    Tambah
                    </button>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>User</th>
                                            <th>Lokasi Slot</th>
                                            <th>Jenis</th>
                                            <th>Waktu Booking</th>
                                            <th>Waktu Mulai</th>
                                            <th>Waktu Selesai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['username']); ?></td>
                                            <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                            <td>
                                                <?php 
                                                if ($row['jenis'] === 'motor') echo "Motor";
                                                elseif ($row['jenis'] === 'mobil') echo "Mobil";
                                                elseif ($row['jenis'] === 'vip') echo "VIP";
                                                else echo "-";
                                                ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['waktu_booking']); ?></td>
                                            <td><?= htmlspecialchars($row['waktu_mulai']); ?></td>
                                            <td><?= htmlspecialchars($row['waktu_selesai']); ?></td>
                                            <td>
                                                <?php 
                                                if ($row['status'] === 'available') {
                                                    echo '<span style="color: green;">Tersedia</span>';
                                                } elseif ($row['status'] === 'booked') {
                                                    echo '<span style="color: red;">Terbooking</span>';
                                                } else {
                                                    echo htmlspecialchars($row['status']);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
              <?php include '../includes/footer.php'; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Modal tambah -->
     <?php
    // Ambil semua user
    $users = $conn->query("SELECT id, username FROM users")->fetchAll(PDO::FETCH_ASSOC);
    // Ambil semua slot parkir
    $slots = $conn->query("SELECT id, lokasi, jenis FROM parkir_slots")->fetchAll(PDO::FETCH_ASSOC);
    ?>
     <!-- Modal Tambah Booking -->
    <div class="modal fade" id="modalTambahBooking" tabindex="-1" role="dialog" aria-labelledby="modalTambahBookingLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="bookingparkir.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahBookingLabel">Tambah Booking</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- User ID hidden -->
                        <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?>">

                        <!-- Slot Parkir -->
                        <div class="form-group">
                            <label for="slot_id">Slot Parkir</label>
                            <select class="form-control" id="slot_id" name="slot_id" required>
                                <option value="">-- Pilih Slot --</option>
                                <?php foreach ($slots as $slot): ?>
                                    <option value="<?= $slot['id'] ?>">
                                        <?= $slot['lokasi'] ?> (<?= ucfirst($slot['jenis']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Jenis Kendaraan -->
                        <div class="form-group">
                            <label for="jenis">Jenis Kendaraan</label>
                            <input type="text" class="form-control" id="jenis" readonly>
                        </div>
                        <!-- Harga -->
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="text" class="form-control" id="harga" readonly>
                        </div>
                        <!-- Waktu Booking -->
                        <div class="form-group">
                            <label for="waktu_booking">Waktu Booking</label>
                            <input type="datetime-local" class="form-control" id="waktu_booking" name="waktu_booking" readonly>
                        </div>

                        <!-- Waktu Mulai -->
                        <div class="form-group">
                            <label for="waktu_mulai">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" name="waktu_mulai" required>
                        </div>

                        <!-- Waktu Selesai -->
                        <div class="form-group">
                            <label for="waktu_selesai">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control" name="waktu_selesai" required>
                        </div>

                        <!-- Status otomatis -->
                        <input type="hidden" name="status" value="pending">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
   <?php include '../includes/modallogout.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Page level custom scripts -->
    <!-- <script src="../js/demo/chart-area-demo.js"></script>
    <script src="../js/demo/chart-pie-demo.js"></script> -->
    <script>
        $(document).ready(function() {
            console.log("DataTable init...");
            $('#dataTable').DataTable({
                "pageLength": 5
            });
        });
    </script>
    <script>
        function editSlot(id, lokasi, status, jenis) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-lokasi').value = lokasi;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-jenis').value = jenis;

            // Tampilkan modal edit
            $('#modalEditSlot').modal('show');
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#modalTambahBooking').on('shown.bs.modal', function () {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Koreksi ke waktu lokal

            const formatted = now.toISOString().slice(0, 16); // Format: yyyy-MM-ddTHH:mm
            document.getElementById('waktu_booking').value = formatted;
        });
    });
    </script>
    <script>
        $(document).ready(function() {
            $('.btn-edit-booking').click(function() {
                $('#edit_booking_id').val($(this).data('id'));
                $('#edit_user_id').val($(this).data('user'));
                $('#edit_slot_id').val($(this).data('slot'));
                $('#edit_waktu_booking').val($(this).data('booking'));
                $('#edit_waktu_mulai').val($(this).data('mulai'));
                $('#edit_waktu_selesai').val($(this).data('selesai'));
                $('#edit_status').val($(this).data('status'));
            });
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const now = new Date();
    const datetimeLocal = now.toISOString().slice(0, 16); // Format: YYYY-MM-DDTHH:mm
    document.getElementById('waktu_booking').value = datetimeLocal;
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slotSelect = document.getElementById('slot_id');
    const jenisField = document.getElementById('jenis');
    const hargaField = document.getElementById('harga');

    slotSelect.addEventListener('change', function () {
        const selectedOption = slotSelect.options[slotSelect.selectedIndex].text;
        const jenisField = document.getElementById('jenis');
        const hargaField = document.getElementById('harga');
        const hiddenJenis = document.getElementById('hidden_jenis');
        const hiddenHarga = document.getElementById('hidden_harga');

        let jenis = '';
        let harga = 0;

        // Cek isi teks full seperti "Parkir A1 (mobil)"
        if (selectedOption.includes('A')) {
            jenis = 'Motor';
            harga = 5000;
        } else if (selectedOption.includes('B')) {
            jenis = 'Mobil';
            harga = 10000;
        } else if (selectedOption.toUpperCase().includes('VIP')) {
            jenis = 'Mobil VIP';
            harga = 50000;
        }

        jenisField.value = jenis;
        hargaField.value = 'Rp ' + harga.toLocaleString('id-ID');

        // Jika menyimpan ke database (hidden input)
        if (hiddenJenis) hiddenJenis.value = jenis;
        if (hiddenHarga) hiddenHarga.value = harga;
    });

});
</script>

</body>

</html>