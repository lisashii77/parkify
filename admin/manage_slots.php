<?php
$currentPage = basename($_SERVER['PHP_SELF']);
include '../config/db.php';
// Tambah atau Edit data slot parkir
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lokasi = $_POST['lokasi'];
    $status = $_POST['status'];
    $jenis = $_POST['jenis'];
    $tempat_id = $_POST['tempat_id'];

    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE parkir_slots SET lokasi = ?, status = ?, jenis = ?, tempat_id = ? WHERE id = ?");
        $stmt->execute([$lokasi, $status, $jenis, $tempat_id, $id]);
    } elseif (isset($_POST['tambah'])) {
        $stmt = $conn->prepare("INSERT INTO parkir_slots (lokasi, status, jenis, tempat_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lokasi, $status, $jenis, $tempat_id]);
    }
    header('Location: manage_slots.php');
    exit;
}

// Hapus data slot parkir
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM parkir_slots WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_slots.php');
    exit;
}

$query = $conn->query("SELECT s.*, t.nama_tempat FROM parkir_slots s JOIN tempat_parkir t ON s.tempat_id = t.tempat_id");
$query = $conn->query("SELECT parkir_slots.*, tempat_parkir.nama_tempat FROM parkir_slots 
JOIN tempat_parkir ON parkir_slots.tempat_id = tempat_parkir.tempat_id");
$tempatList = $conn->query("SELECT * FROM tempat_parkir")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Slot Parkir</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
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

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Slot Parkir</h1>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#modalTambahSlot">
                    Tambah
                    </button>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tempat</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th>Jenis</th>
                                            <th>Harga</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['nama_tempat']); ?></td>
                                            <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                             <td>
                                                <?php if ($row['jenis'] === 'motor'): ?>
                                                    <span>Motor</span>
                                                <?php elseif ($row['jenis'] === 'mobil'): ?>
                                                    <span>Mobil</span>
                                                <?php else: ?>
                                                    <span>VIP</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] === 'available'): ?>
                                                    <span style="color: green;">Tersedia</span>
                                                <?php else: ?>
                                                    <span style="color: red;">Terbooking</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                            </td>
                                             <td>
                                                <!-- Tombol Edit -->
                                            <button 
                                                onclick='editSlot(
                                                    <?= $row['id'] ?>, 
                                                    <?= json_encode($row['lokasi']) ?>, 
                                                    <?= json_encode($row['status']) ?>, 
                                                    <?= json_encode($row['jenis']) ?>,
                                                    <?= $row['tempat_id'] ?>
                                                )' 
                                                class="btn btn-warning">Edit
                                            </button>
                                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

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
    <!-- Modal tambah -->
    <div class="modal fade" id="modalTambahSlot" tabindex="-1" role="dialog" aria-labelledby="modalTambahSlotLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="manage_slots.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahSlotLabel">Tambah Slot Parkir</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Tambahkan modal-body untuk membungkus semua isian form -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tempat_id">Tempat</label>
                            <select class="form-control" id="tempat_id" name="tempat_id" required>
                                <option value="">-- Pilih Tempat --</option>
                                <?php foreach ($tempatList as $t): ?>
                                    <option value="<?= $t['tempat_id'] ?>"><?= $t['nama_tempat'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                        </div>

                        <div class="form-group">
                            <label for="jenis">Jenis</label>
                            <select class="form-control" id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="motor">Motor</option>
                                <option value="mobil">Mobil</option>
                                <option value="vip">VIP</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="available">Tersedia</option>
                                <option value="booked">Terbooking</option>
                            </select>
                        </div>
                    </div>

                    <!-- Footer tombol -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Logout Modal-->
     <!-- modal edit -->
      <div class="modal fade" id="modalEditSlot" tabindex="-1" role="dialog" aria-labelledby="modalEditSlotLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="manage_slots.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditSlotLabel">Edit Slot Parkir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-tempat_id">Tempat</label>
                        <select class="form-control" id="edit-tempat_id" name="tempat_id" required>
                            <option value="">-- Pilih Tempat --</option>
                            <?php foreach ($tempatList as $t): ?>
                                <option value="<?= $t['tempat_id'] ?>"><?= $t['nama_tempat'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-lokasi">Lokasi</label>
                        <input type="text" class="form-control" id="edit-lokasi" name="lokasi" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-jenis">Jenis</label>
                        <select class="form-control" id="edit-jenis" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select class="form-control" id="edit-status" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="available">Tersedia</option>
                            <option value="booked">Terbooking</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" name="edit" class="btn btn-primary">Update</button>
                </div>
            </div>
            </form>
        </div>
        </div>

   <?php include '../includes/modallogout.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <!-- <script src="../js/demo/chart-area-demo.js"></script>
    <script src="../js/demo/chart-pie-demo.js"></script> -->
    <script>
        $(document).ready(function() {
            console.log("DataTable init...");
            $('#dataTable').DataTable({
                "pageLength": 5
            });
        });
    </script>
    <script>
    function editSlot(id, lokasi, status, jenis, tempat_id) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-lokasi').value = lokasi;
        document.getElementById('edit-status').value = status;
        document.getElementById('edit-jenis').value = jenis;
        document.getElementById('edit-tempat_id').value = tempat_id;

        $('#modalEditSlot').modal('show');
    }
    </script>
</body>

</html>