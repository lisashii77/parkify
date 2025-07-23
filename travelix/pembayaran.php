<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}
include '../config/db.php';

$slot_id = $_GET['slot_id'] ?? null;
$jam_masuk = $_GET['jam_masuk'] ?? null;
$jam_keluar = $_GET['jam_keluar'] ?? null;

if (!$slot_id || !$jam_masuk || !$jam_keluar) {
    die("Parameter tidak lengkap.");
}

$user_id = $_SESSION['user']['id'];

// Cek booking
$stmt_booking = $conn->prepare("SELECT * FROM bookings WHERE slot_id = ? AND user_id = ?");
$stmt_booking->execute([$slot_id, $user_id]);
$booking = $stmt_booking->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    $stmt_insert = $conn->prepare("INSERT INTO bookings (user_id, slot_id, waktu_mulai, waktu_selesai) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$user_id, $slot_id, $jam_masuk, $jam_keluar]);
    $booking_id = $conn->lastInsertId();
} else {
    $booking_id = $booking['booking_id'];
}

// Ambil data slot dan pembayaran
$stmt = $conn->prepare("
   SELECT ps.*, tp.nama_tempat, b.booking_id, b.waktu_mulai, b.waktu_selesai,
       p.id AS pembayaran_id, p.status, p.metode_pembayaran, p.harga, p.no_invoice
    FROM parkir_slots ps
    JOIN tempat_parkir tp ON ps.tempat_id = tp.tempat_id
    LEFT JOIN bookings b ON b.slot_id = ps.id AND b.user_id = ?
    LEFT JOIN pembayaran p ON p.booking_id = b.booking_id
    WHERE ps.id = ?
    ORDER BY p.waktu_pembayaran DESC
    LIMIT 1
");
$stmt->execute([$user_id, $slot_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Tentukan harga berdasarkan jenis
function getHargaByJenis($jenis) {
    switch (strtolower($jenis)) {
        case 'motor': return 5000;
        case 'mobil': return 10000;
        case 'vip':   return 50000;
        default:      return 0;
    }
}
$harga_flat = getHargaByJenis($data['jenis'] ?? '');

// Proses form pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode = $_POST['metode_pembayaran'];
    $bukti = $_FILES['bukti_pembayaran'];

    if ($bukti['error'] == 0) {
        $ext = pathinfo($bukti['name'], PATHINFO_EXTENSION);
        $filename = 'bukti_' . time() . '.' . $ext;
        move_uploaded_file($bukti['tmp_name'], '../uploads/' . $filename);

        if ($data['pembayaran_id']) {
            $query = $conn->prepare("UPDATE pembayaran SET metode_pembayaran=?, bukti_pembayaran=?, status='menunggu_verifikasi', harga=? WHERE id=?");
            $query->execute([$metode, $filename, $harga_flat, $data['pembayaran_id']]);
        } else {
           $invoice_number = 'INV' . date('Ymd') . '-' . mt_rand(1000, 9999);
            $query = $conn->prepare("INSERT INTO pembayaran (booking_id, metode_pembayaran, bukti_pembayaran, status, harga, no_invoice) VALUES (?, ?, ?, 'menunggu_verifikasi', ?, ?)");
            $query->execute([$booking_id, $metode, $filename, $harga_flat, $invoice_number]);
        }

        header("Location: pembayaran.php?slot_id=$slot_id&jam_masuk=$jam_masuk&jam_keluar=$jam_keluar&status=waiting");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/offers_styles.css">
    <link rel="stylesheet" href="styles/offers_responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="super_container">
<?php include '../travelix/include/navbar.php'; ?>

<div class="home">
    <div class="home_background parallax-window" data-parallax="scroll" data-image-src="images/payment.png"></div>
    <div class="home_content">
        <div class="home_title">PEMBAYARAN</div>
    </div>
</div>

<section style="background-color: white; padding-top: 100px; padding-bottom: 50px;">
<div class="container py-5">
  <div class="card">
    <div class="card-body" style="background-color: #f9f9f9;">
      <div class="row d-flex justify-content-center pb-5">
        <div class="col-md-7 col-xl-5 mb-4 mb-md-0">
          <div class="py-4 d-flex flex-row">
            <h5><b>STATUS</b> | </h5>
            <h5><?= strtoupper($data['status'] ?? 'BELUM DIBAYAR') ?></h5>
          </div>
          <h4 class="text-success">Rp<?= number_format($harga_flat, 0, ',', '.') ?></h4>
          <h5><?= htmlspecialchars($data['nama_tempat']) ?> - <?= htmlspecialchars($data['lokasi']) ?></h5>

          <div class="d-flex pt-2">
            <p><b>Jenis Kendaraan: <span class="text-dark"><?= ucfirst($data['jenis']) ?></span></b></p>
          </div>
          <p>Silakan lakukan pembayaran untuk melanjutkan proses booking slot parkir.</p>
          <hr />
          <div class="pt-2">
            <p><b>Durasi Booking:</b><br>
            <?= $jam_masuk ? date("d M Y H:i", strtotime($jam_masuk)) : '-' ?> - 
            <?= $jam_keluar ? date("d M Y H:i", strtotime($jam_keluar)) : '-' ?>
            </p>

            <?php if ($data['status'] === 'berhasil'): ?>
                <div class="alert alert-success">Pembayaran Berhasil</div>
            <?php elseif ($data['status'] === 'menunggu_verifikasi'): ?>
                <div class="alert alert-warning">Menunggu Verifikasi Admin</div>
            <?php else: ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-control" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Dompet Digital">Dompet Digital</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Upload Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
            </form>
            <?php endif; ?>
          </div>
        </div>

        <div class="col-md-5 col-xl-4 offset-xl-1">
          <div class="rounded d-flex flex-column p-2 bg-light">
            <div class="p-2 me-3">
              <h4>Ringkasan Pembayaran</h4>
            </div>
            <div class="p-2 d-flex">
              <div class="col-8">Harga Slot</div>
              <div class="ms-auto">Rp<?= number_format($harga_flat, 0, ',', '.') ?></div>
            </div>
            <div class="p-2 d-flex">
              <div class="col-8">Status</div>
              <div class="ms-auto"><?= ucfirst($data['status'] ?? 'Belum Bayar') ?></div>
            </div>
            <hr />
            <div class="p-2 d-flex pt-2">
              <div class="col-8"><b>Total</b></div>
              <div class="ms-auto"><b class="text-success">Rp<?= number_format($harga_flat, 0, ',', '.') ?></b></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</section>

<?php include '../travelix/include/footer.php'; ?>
</div>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="styles/bootstrap4/popper.js"></script>
<script src="styles/bootstrap4/bootstrap.min.js"></script>
<script src="plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="plugins/easing/easing.js"></script>
<script src="plugins/parallax-js-master/parallax.min.js"></script>
<script src="js/offers_custom.js"></script>

<?php if (isset($_GET['status']) && $_GET['status'] === 'waiting'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Bukti Pembayaran Dikirim',
            text: 'Terima kasih, bukti pembayaran berhasil dikirim. Menunggu verifikasi dari admin.',
            confirmButtonText: 'OK'
        });
    });
</script>
<?php endif; ?>
</body>
</html>
