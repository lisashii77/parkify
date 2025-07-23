<?php
include '../config/db.php';

$jenis = $_POST['jenis'] ?? '';
$tempat_id = $_POST['tempat'] ?? '';
$lokasi = $_POST['lokasi'] ?? '';
$jam_masuk = $_POST['jam_masuk'] ?? '';
$jam_keluar = $_POST['jam_keluar'] ?? '';

// Ambil slot beserta status pembayaran terakhir
$stmt = $conn->prepare("
    SELECT ps.*, tp.nama_tempat, 
        (
            SELECT p.status 
            FROM pembayaran p 
            JOIN bookings b ON p.booking_id = b.booking_id 
            WHERE b.slot_id = ps.id 
            ORDER BY p.waktu_pembayaran DESC 
            LIMIT 1
        ) AS status_pembayaran
    FROM parkir_slots ps
    JOIN tempat_parkir tp ON ps.tempat_id = tp.tempat_id
    WHERE ps.tempat_id = ? AND ps.lokasi = ? AND ps.jenis = ?
");
$stmt->execute([$tempat_id, $lokasi, $jenis]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update status slot jika perlu
foreach ($data as $row) {
    $slot_id = $row['id'];
    $status_pembayaran = $row['status_pembayaran'];
    $status_slot = $row['status'];

    $new_status = in_array($status_pembayaran, ['gagal', 'selesai', null]) ? 'available' : 'booked';

    if ($status_slot !== $new_status) {
        $update = $conn->prepare("UPDATE parkir_slots SET status = ? WHERE id = ?");
        $update->execute([$new_status, $slot_id]);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian Parkir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Travelix CSS -->
    <link rel="stylesheet" href="styles/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/main_styles.css">
    <link rel="stylesheet" href="styles/responsive.css">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if (empty($data)): ?>
                <p class="text-center">Tidak ada hasil ditemukan.</p>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <?php
                        $status_pembayaran = $row['status_pembayaran'];
                        $is_available = in_array($status_pembayaran, ['gagal', 'selesai', null]);
                    ?>
                    <div class="offers_item mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="offers_image_container">
                                    <div class="offers_image_background" style="background-image:url('images/intro_1.jpg'); height: 200px; background-size: cover; background-position: center;"></div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="offers_content">
                                    <h5 class="offers_price mb-2"><?= htmlspecialchars($row['nama_tempat']) ?></h5>

                                    <p class="offers_text mb-2">
                                        🅿️ <strong>Lokasi Slot:</strong> <?= htmlspecialchars($row['lokasi']) ?><br>
                                        🚗 <strong>Jenis Kendaraan:</strong> <?= ucfirst($row['jenis']) ?><br>
                                        💰 <strong>Harga:</strong> Rp<?= number_format($row['harga'], 0, ',', '.') ?><br>
                                        🔄 <strong>Status:</strong>
                                        <?php if ($is_available): ?>
                                            <span class="text-success">Tersedia</span>
                                        <?php else: ?>
                                            <span class="text-danger">Tidak Tersedia</span>
                                        <?php endif; ?>
                                    </p>

                                    <div class="search_item mb-2">
                                        <?php if ($is_available): ?>
                                            <a href="pembayaran.php?slot_id=<?= htmlspecialchars($row['id']) ?>&jam_masuk=<?= urlencode($_POST['jam_masuk']) ?>&jam_keluar=<?= urlencode($_POST['jam_keluar']) ?>">
                                                <button type="button" class="button search_button">Booking Sekarang</button>
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="button search_button" disabled>Slot Tidak Tersedia</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="styles/bootstrap4/popper.js"></script>
<script src="styles/bootstrap4/bootstrap.min.js"></script>
<script src="plugins/easing/easing.js"></script>
<script src="js/custom.js"></script>

</body>
</html>
