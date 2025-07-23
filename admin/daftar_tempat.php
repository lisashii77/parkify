<?php
include '../config/db.php';
$currentPage = basename($_SERVER['PHP_SELF']);

// Tambah atau Edit Tempat
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_tempat = $_POST['nama_tempat'];

    if (isset($_POST['tambah'])) {
        $stmt = $conn->prepare("INSERT INTO tempat_parkir (nama_tempat) VALUES (?)");
        $stmt->execute([$nama_tempat]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['tempat_id'];
        $stmt = $conn->prepare("UPDATE tempat_parkir SET nama_tempat = ? WHERE tempat_id = ?");
        $stmt->execute([$nama_tempat, $id]);
    }
    header("Location: manage_tempat.php");
    exit;
}

// Hapus Tempat
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tempat_parkir WHERE tempat_id = ?");
    $stmt->execute([$id]);
    header("Location: manage_tempat.php");
    exit;
}

// Ambil Data
$tempatList = $conn->query("SELECT * FROM tempat_parkir")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Daftar Tempat</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
</head>
<body id="page-top">
<div id="wrapper">
    <?php include '../includes/adminsidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../includes/adminnavbar.php'; ?>
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Daftar Tempat Parkir</h1>

                <button class="btn btn-success mb-2" data-toggle="modal" data-target="#modalTambah">Tambah</button>

                <div class="card shadow">
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Tempat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($tempatList as $t): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($t['nama_tempat']) ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm"
                                                    onclick="editTempat(<?= $t['tempat_id'] ?>, <?= json_encode($t['nama_tempat']) ?>)">
                                                Edit
                                            </button>
                                            <a href="?delete=<?= $t['tempat_id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus tempat ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <?php include '../includes/footer.php'; ?>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tempat</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Tempat</label>
                        <input type="text" name="nama_tempat" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <input type="hidden" name="tempat_id" id="edit-id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tempat</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Tempat</label>
                        <input type="text" name="nama_tempat" id="edit-nama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="edit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function editTempat(id, nama) {
    $('#edit-id').val(id);
    $('#edit-nama').val(nama);
    $('#modalEdit').modal('show');
}

$('#dataTable').DataTable({
    "pageLength": 5
});

</script>

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
