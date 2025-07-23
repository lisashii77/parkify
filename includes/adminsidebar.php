<?php
// Jika belum didefinisikan di file yang include ini
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
?>

<!-- SIDEBAR ADMIN -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-text mx-3">PARKIFY</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item <?= $currentPage == 'index.php' ? 'active' : '' ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <!-- Data Pengguna -->
    <li class="nav-item <?= $currentPage == 'data_pengguna.php' ? 'active' : '' ?>">
        <a class="nav-link" href="data_pengguna.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pengguna</span>
        </a>
    </li>
    <li class="nav-item <?= $currentPage == 'daftar_tempat.php' ? 'active' : '' ?>">
        <a class="nav-link" href="daftar_tempat.php">
            <i class="fas fa-fw fa-car"></i>
            <span>Daftar Tempat</span>
        </a>
    </li>

    <!-- Slot Parkir -->
    <li class="nav-item <?= $currentPage == 'manage_slots.php' ? 'active' : '' ?>">
        <a class="nav-link" href="manage_slots.php">
            <i class="fas fa-fw fa-parking"></i>
            <span>Slot Parkir</span>
        </a>
    </li>

    <!-- Data Booking -->
    <li class="nav-item <?= $currentPage == 'manage_bookings.php' ? 'active' : '' ?>">
        <a class="nav-link" href="manage_bookings.php">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Data Booking</span>
        </a>
    </li>

    <!-- Pembayaran -->
    <li class="nav-item <?= $currentPage == 'adminbayar.php' ? 'active' : '' ?>">
        <a class="nav-link" href="adminbayar.php">
            <i class="fas fa-fw fa-money-bill"></i>
            <span>Pembayaran</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
