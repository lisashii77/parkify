<?php
$currentPage = basename($_SERVER['PHP_SELF']);
session_start();
include '../config/db.php';

// Validasi input GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$aksi = $_GET['aksi'] ?? null;

if (!$id || !in_array($aksi, ['terima', 'tolak'])) {
    $_SESSION['error'] = "Permintaan tidak valid.";
    header('Location: adminbayar.php');
    exit;
}

if ($aksi == 'terima') {
    try {
        // Mulai transaksi
        $conn->beginTransaction();

        // 1. Update status pembayaran
        $stmt = $conn->prepare("UPDATE pembayaran SET status = 'berhasil' WHERE id = ?");
        $stmt->execute([$id]);

        // 2. Ambil booking_id
        $stmt = $conn->prepare("SELECT booking_id FROM pembayaran WHERE id = ?");
        $stmt->execute([$id]);
        $booking_id = $stmt->fetchColumn();

        if (!$booking_id) {
            throw new Exception("Booking ID tidak ditemukan.");
        }

        // 3. Update status booking ke 'dibayar'
        $stmt = $conn->prepare("UPDATE bookings SET status = 'dibayar' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);

        // 4. Ambil slot_id dari booking
        $stmt = $conn->prepare("SELECT slot_id FROM bookings WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $slot_id = $stmt->fetchColumn();

        if (!$slot_id) {
            throw new Exception("Slot ID tidak ditemukan.");
        }

        // 5. Update status slot menjadi 'booked'
        $stmt = $conn->prepare("UPDATE parkir_slots SET status = 'booked' WHERE id = ?");
        $stmt->execute([$slot_id]);

        // Commit jika semua berhasil
        $conn->commit();
        $_SESSION['sukses'] = "Pembayaran diterima dan slot berhasil dipesan.";
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollBack();
        $_SESSION['error'] = "Gagal memproses: " . $e->getMessage();
    }

} elseif ($aksi == 'tolak') {
    // Tolak pembayaran
    $stmt = $conn->prepare("UPDATE pembayaran SET status = 'ditolak' WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['sukses'] = "Pembayaran ditolak.";
}

// Redirect kembali ke halaman admin
header('Location: adminbayar.php');
exit;
