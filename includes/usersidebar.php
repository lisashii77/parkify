<?php
$currentPage = basename($_SERVER['PHP_SELF']); // Tambahkan baris ini
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-text mx-3">Parkir App User</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= $currentPage == 'index.php' ? 'active' : '' ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item <?= $currentPage == 'userslot.php' ? 'active' : '' ?>">
        <a class="nav-link" href="userslot.php">
            <i class="fas fa-fw fa-car"></i>
            <span>Slot Parkir</span>
        </a>
    </li>

    <li class="nav-item <?= $currentPage == 'bookingparkir.php' ? 'active' : '' ?>">
        <a class="nav-link" href="bookingparkir.php">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Booking Parkir</span>
        </a>
    </li>

    <li class="nav-item <?= $currentPage == 'pembayaran.php' ? 'active' : '' ?>">
        <a class="nav-link" href="pembayaran.php">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pembayaran</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
