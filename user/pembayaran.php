<?php
session_start();
include '../config/db.php';
// Update status booking menjadi 'selesai' dan slot menjadi 'available' jika waktu_selesai sudah lewat
date_default_timezone_set('Asia/Jakarta');

$conn->prepare("
    UPDATE bookings 
    SET status = 'selesai' 
    WHERE status = 'dibayar' 
      AND waktu_selesai <= NOW()
")->execute();

$conn->prepare("
    UPDATE parkir_slots 
    SET status = 'available' 
    WHERE id IN (
        SELECT slot_id FROM bookings 
        WHERE status = 'selesai' 
          AND waktu_selesai <= NOW()
    )
")->execute();

// Cancel otomatis booking yang belum dibayar dalam 60 menit
$conn->prepare("
    UPDATE bookings 
    SET status = 'dibatalkan' 
    WHERE status = 'pending' 
      AND waktu_booking <= DATE_SUB(NOW(), INTERVAL 60 MINUTE)
")->execute();
// 2. Update status slot menjadi available jika booking dibatalkan
$conn->prepare("
    UPDATE parkir_slots 
    SET status = 'available' 
    WHERE id IN (
        SELECT slot_id 
        FROM bookings 
        WHERE status = 'dibatalkan' 
          AND waktu_booking <= DATE_SUB(NOW(), INTERVAL 60 MINUTE)
    )
")->execute();
//amil data dari database
$query = $conn->query("
    SELECT b.booking_id, b.user_id, u.username, b.slot_id, s.lokasi, b.waktu_booking, b.waktu_mulai, b.waktu_selesai, b.status, s.jenis
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN parkir_slots s ON b.slot_id = s.id
");

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
    SELECT s.* 
    FROM parkir_slots s
    LEFT JOIN bookings b 
        ON s.id = b.slot_id 
        AND b.status IN ('pending', 'dibayar')
    WHERE b.slot_id IS NULL
");

$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses kirim bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_bukti'])) {
    $booking_id = $_POST['booking_id'];
    $metode_pembayaran = $_POST['metode']; // sesuai nama kolom di DB

    // Validasi upload
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bukti_pembayaran']['tmp_name'];
        $fileName = $_FILES['bukti_pembayaran']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'bukti_' . time() . '_' . $booking_id . '.' . $fileExtension;

        $uploadDir = '../uploads/';
        $destPath = $uploadDir . $newFileName;

        // Buat folder jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Proses upload file
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // 1. Update status booking ke 'dibayar'
            // $stmt = $conn->prepare("UPDATE bookings SET status = 'dibayar' WHERE booking_id = ?");
            // $stmt->execute([$booking_id]);
            
            // 2. Ambil harga dari slot yang dibooking
            $stmt = $conn->prepare("
                SELECT s.harga 
                FROM bookings b 
                JOIN parkir_slots s ON b.slot_id = s.id 
                WHERE b.booking_id = ?
            ");
            $stmt->execute([$booking_id]);
            $harga = $stmt->fetchColumn();
            
            // 3. Simpan ke tabel pembayaran (termasuk harga)
            $stmt = $conn->prepare("
                INSERT INTO pembayaran (
                    booking_id,
                    metode_pembayaran,
                    bukti_pembayaran,
                    waktu_pembayaran,
                    status,
                    harga
                ) VALUES (?, ?, ?, NOW(), 'menunggu_verifikasi', ?)
            ");
            $stmt->execute([$booking_id, $metode_pembayaran, $newFileName, $harga]);

            // 3. Update status slot menjadi 'booked'
            // Ambil slot_id dari booking
            $stmt = $conn->prepare("SELECT slot_id FROM bookings WHERE booking_id = ?");
            $stmt->execute([$booking_id]);
            $slot_id = $stmt->fetchColumn();

            // Update status slot menjadi 'booked'
            // if ($slot_id) {
            //     $stmt = $conn->prepare("UPDATE parkir_slots SET status = 'booked' WHERE id = ?");
            //     $stmt->execute([$slot_id]);
            // }

            $_SESSION['sukses'] = "Bukti pembayaran berhasil dikirim.";
        } else {
            $_SESSION['error'] = "Gagal mengupload bukti pembayaran.";
        }
    } else {
        $_SESSION['error'] = "File tidak valid atau gagal upload.";
    }

    header('Location: pembayaran.php');
    exit;
}

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
                    <h1 class="h3 mb-2 text-gray-800">Pembayaran</h1>
                    <!-- Button trigger modal -->
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
                                            <th>Aksi</th>
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
                                          <td>
                                            <?php if ($row['status'] === 'pending') : ?>
                                                <button class="btn btn-success btn-sm btn-bayar"
                                                        data-booking-id="<?= $row['booking_id'] ?>"
                                                        data-toggle="modal"
                                                        data-target="#modalBayar">
                                                    Bayar
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    Sudah Bayar
                                                </button>
                                            <?php endif; ?>
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
    <!-- Modal Pembayaran -->
    <div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="modalBayarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="pembayaran.php" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" id="booking_id_bayar">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    <div class="form-group">
                        <label for="metode">Metode Pembayaran</label>
                        <select name="metode" id="metode" class="form-control" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer_bank">Transfer Bank</option>
                            <option value="e_wallet">Dompet Digital</option>
                        </select>
                    </div>

                    <!-- Detail QRIS -->
                    <div class="form-group metode-detail" id="qris_detail" style="display: none;">
                        <label>Scan QRIS:</label><br>
                        <div class="text-center">
                            <img src="../img/frame.png" alt="QRIS" width="200">
                        </div>
                    </div>

                    <!-- Detail Transfer Bank -->
                    <div class="form-group metode-detail" id="bank_detail" style="display: none;">
                        <label>Nomor Rekening:</label>
                        <p>1234567890 (BCA - a.n. PT Parkir Online)</p>
                    </div>

                    <!-- Detail eWallet -->
                    <div class="form-group metode-detail" id="ewallet_detail" style="display: none;">
                        <label>Nomor e-Wallet:</label>
                        <p>0857-1234-5678 (Dana / OVO / GoPay)</p>
                    </div>

                    <!-- Upload Bukti -->
                    <div class="form-group">
                        <label for="bukti_pembayaran">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" class="form-control-file" required accept="image/*">
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="kirim_bukti" class="btn btn-primary">Kirim Bukti</button>
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
    $(document).ready(function () {
        $('.btn-bayar').on('click', function () {
            const bookingId = $(this).data('booking-id');
            $('#booking_id_bayar').val(bookingId);
        });
    });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tampilkan detail metode pembayaran sesuai pilihan
            document.getElementById('metode').addEventListener('change', function () {
                const metode = this.value;

                // Sembunyikan semua metode detail
                document.querySelectorAll('.metode-detail').forEach(el => {
                    el.style.display = 'none';
                });

                if (metode === 'qris') {
                    document.getElementById('qris_detail').style.display = 'block';
                } else if (metode === 'transfer_bank') {
                    document.getElementById('bank_detail').style.display = 'block';
                } else if (metode === 'e_wallet') {
                    document.getElementById('ewallet_detail').style.display = 'block';
                }
            });
        });
    </script>
</body>

</html>