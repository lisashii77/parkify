<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}
include '../config/db.php';

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("
    SELECT tp.nama_tempat, ps.lokasi, ps.jenis, b.waktu_mulai, b.waktu_selesai,
           p.harga, p.status, p.metode_pembayaran, p.bukti_pembayaran, p.waktu_pembayaran, p.no_invoice
    FROM pembayaran p
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN parkir_slots ps ON b.slot_id = ps.id
    JOIN tempat_parkir tp ON ps.tempat_id = tp.tempat_id
    WHERE b.user_id = ?
    ORDER BY p.waktu_pembayaran DESC
");
$stmt->execute([$user_id]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pembayaran</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="styles/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/offers_styles.css">
    <link rel="stylesheet" href="styles/offers_responsive.css">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- QR Modal Style -->
    <style>
        .qr-thumbnail {
            width: 80px;
            height: 80px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="super_container">
    <?php include '../travelix/include/navbar.php'; ?>

    <div class="home">
        <div class="home_background parallax-window" data-parallax="scroll" data-image-src="images/riwayatbayar2.jpg"></div>
        <div class="home_content">
            <div class="home_title">RIWAYAT PEMBAYARAN</div>
        </div>
    </div>

    <div class="container mt-5">
        <h3>Riwayat Pembayaran Anda</h3>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Tempat</th>
                    <th>Lokasi</th>
                    <th>Jenis</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Bukti</th>
                    <th>QR Code</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['no_invoice'] ?? '-') ?></td>
                    <td><?= date('d M Y H:i', strtotime($row['waktu_mulai'])) ?></td>
                    <td><?= htmlspecialchars($row['nama_tempat']) ?></td>
                    <td><?= htmlspecialchars($row['lokasi']) ?></td>
                    <td><?= ucfirst($row['jenis']) ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
                    <td>
                        <?php if ($row['bukti_pembayaran']): ?>
                            <a href="../uploads/<?= $row['bukti_pembayaran'] ?>" target="_blank">Lihat</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'berhasil' && $row['no_invoice']): ?>
                            <img class="qr-thumbnail"
                                src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($row['no_invoice']) ?>&size=100x100"
                                alt="QR Code"
                                data-toggle="modal"
                                data-target="#qrModal"
                                data-src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($row['no_invoice']) ?>&size=300x300">
                            <small class="d-block text-muted">Klik untuk perbesar</small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include '../travelix/include/footer.php'; ?>
</div>

<!-- Modal QR -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title w-100">QR Masuk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="qrLargeImage" src="" alt="QR Code Besar" style="max-width: 300px;">
        <p class="mt-2">Tunjukkan kode ini ke petugas parkir.</p>
      </div>
    </div>
  </div>
</div>

<!-- Script -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="styles/bootstrap4/popper.js"></script>
<script src="styles/bootstrap4/bootstrap.min.js"></script>
<script src="plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="plugins/easing/easing.js"></script>
<script src="plugins/parallax-js-master/parallax.min.js"></script>
<script src="js/offers_custom.js"></script>

<!-- QR Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qrLarge = document.getElementById('qrLargeImage');
        document.querySelectorAll('.qr-thumbnail').forEach(img => {
            img.addEventListener('click', function () {
                const src = this.getAttribute('data-src');
                qrLarge.setAttribute('src', src);
            });
        });
    });
</script>

<!-- SweetAlert setelah kirim bukti -->
<?php if (isset($_GET['status']) && $_GET['status'] === 'waiting'): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Bukti Dikirim',
        text: 'Terima kasih, bukti pembayaran Anda sedang diverifikasi.',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
</body>
</html>
