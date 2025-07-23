<?php
include '../config/db.php';

header('Content-Type: text/html; charset=UTF-8');

if (isset($_POST['tempat_id'], $_POST['jenis'])) {
    $tempat_id = $_POST['tempat_id'];
    $jenis = $_POST['jenis'];

    // Validasi input (opsional, tapi baik untuk keamanan tambahan)
    if (!is_numeric($tempat_id) || !in_array($jenis, ['motor', 'mobil', 'vip'])) {
        echo '<option value="">Data tidak valid</option>';
        exit;
    }

    $stmt = $conn->prepare("SELECT lokasi FROM parkir_slots WHERE tempat_id = ? AND jenis = ?");
    $stmt->execute([$tempat_id, $jenis]);
    $lokasi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<option value="">Pilih Lokasi</option>';
    if (count($lokasi_list) > 0) {
        foreach ($lokasi_list as $l) {
            echo '<option value="' . htmlspecialchars($l['lokasi']) . '">' . htmlspecialchars($l['lokasi']) . '</option>';
        }
    } else {
        echo '<option value="">Tidak ada lokasi tersedia</option>';
    }
}
?>
