<?php
$host = 'localhost';
$db   = 'parkings_app'; // Ganti dengan nama database kamu
$user = 'root';        // Ganti jika kamu pakai user lain
$pass = '';            // Ganti jika ada password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
