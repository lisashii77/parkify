<?php
include '../config/db.php';

if (isset($_GET['tempat_id'])) {
    $tempat_id = $_GET['tempat_id'];
    $stmt = $conn->prepare("SELECT id, lokasi, jenis FROM parkir_slots WHERE tempat_id = ?");
    $stmt->execute([$tempat_id]);
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($slots as $slot) {
        echo "<option value='{$slot['id']}'>{$slot['lokasi']} (" . ucfirst($slot['jenis']) . ")</option>";
    }
}
?>
