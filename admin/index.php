<?php
$currentPage = basename($_SERVER['PHP_SELF']);
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
include '../config/db.php';
//statistik
$slots = $conn->query("SELECT * FROM parkir_slots ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$currentPage = basename($_SERVER['PHP_SELF']); // ambil nama file saat ini
$totalUsers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalSlots = $conn->query("SELECT COUNT(*) FROM parkir_slots")->fetchColumn();
$availableSlots = $conn->query("SELECT COUNT(*) FROM parkir_slots WHERE status = 'available'")->fetchColumn();
$bookedSlots = $conn->query("SELECT COUNT(*) FROM parkir_slots WHERE status = 'booked'")->fetchColumn();
$totalPembayaran = $conn->query("SELECT COUNT(*) FROM pembayaran")->fetchColumn();

// Data chart jenis kendaraan
$jenisData = $conn->query("
    SELECT jenis, COUNT(*) as jumlah 
    FROM parkir_slots 
    GROUP BY jenis
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
         <?php include '../includes/adminsidebar.php'; ?>

        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                 <?php include '../includes/adminnavbar.php'; ?>
                <!-- End of Topbar -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Dashboard Admin</h1>

                    <div class="row">

                        <!-- Total Pengguna -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengguna</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slot Parkir -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Slot Tersedia</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $availableSlots ?> dari <?= $totalSlots ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-parking fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slot Booked -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Slot Terbooking</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $bookedSlots ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Transaksi -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-dark shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Transaksi</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPembayaran ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-receipt fa-2x text-dark"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Chart Jenis Slot -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Slot Parkir Berdasarkan Jenis</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="jenisChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


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

    <!-- Logout Modal-->
   <?php include '../includes/modallogout.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/chart-area-demo.js"></script>
    <script src="../js/demo/chart-pie-demo.js"></script>
    <script>
        const ctx = document.getElementById('jenisChart').getContext('2d');
        const jenisChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($jenisData, 'jenis')) ?>,
                datasets: [{
                    label: 'Jumlah',
                    data: <?= json_encode(array_column($jenisData, 'jumlah')) ?>,
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e'],
                }]
            }
        });
    </script>

</body>

</html>