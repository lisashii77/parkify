<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPage = basename($_SERVER['PHP_SELF']);
include '../config/db.php';
$query = $conn->query("SELECT id, username, role FROM users");

// Hapus pengguna
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: data_pengguna.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $role, $id]);

    header("Location: data_pengguna.php");
    exit;
}
// Tambah pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_user'])) {
    $username = $_POST['tambah_username'];
    $password = password_hash($_POST['tambah_password'], PASSWORD_DEFAULT);
    $role = $_POST['tambah_role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    header("Location: data_pengguna.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Data Pengguna</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
                    <h1 class="h3 mb-2 text-gray-800">Data Pengguna</h1>
                    <!-- Button trigger modal -->
                   <!-- Tombol untuk membuka modal tambah -->
                    <button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#modalTambahUser">
                    Tambah
                    </button>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTableUsers" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Role</th> <!-- Tambahkan jika pakai sistem multi-role -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['username']); ?></td>
                                            <td><?= htmlspecialchars($row['role'] ?? 'User'); ?></td> <!-- fallback jika kolom role belum ada -->
                                            <td>
                                                <!-- Edit -->
                                                <button type="button"
                                                    class="btn btn-warning btn-sm btn-edit-user"
                                                    data-id="<?= $row['id']; ?>"
                                                    data-username="<?= $row['username']; ?>"
                                                    data-role="<?= $row['role']; ?>">
                                                    Edit
                                                </button>
                                                <!-- Hapus -->
                                                <a href="?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                                    Hapus
                                                </a>
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
     <?php
    // Ambil semua user
    $users = $conn->query("SELECT id, username FROM users")->fetchAll(PDO::FETCH_ASSOC);
    // Ambil semua slot parkir
    $slots = $conn->query("SELECT id, lokasi, jenis FROM parkir_slots")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="modalTambahUser" tabindex="-1" role="dialog" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="data_pengguna.php">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalTambahUserLabel">Tambah Pengguna</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <!-- Username -->
            <div class="form-group">
                <label for="tambah_username">Username</label>
                <input type="text" class="form-control" id="tambah_username" name="tambah_username" required>
            </div>
            <!-- Password -->
          <!-- Password -->
            <div class="form-group">
            <label for="tambah_password">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="tambah_password" name="tambah_password" required>
                <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                </div>
            </div>
            </div>
            <!-- Role -->
            <div class="form-group">
                <label for="tambah_role">Role</label>
                <select class="form-control" id="tambah_role" name="tambah_role" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
                </select>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_user" class="btn btn-primary">Simpan</button>
            </div>
        </div>
        </form>
    </div>
    </div>
    <!-- modal edit -->
    <div class="modal fade" id="modalEditUser" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form method="POST" action="data_pengguna.php">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <input type="hidden" id="edit_user_id" name="id">

                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" class="form-control" id="edit_username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select class="form-control" id="edit_role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    </select>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" name="update_user" class="btn btn-primary">Simpan Perubahan</button>
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
    document.addEventListener('DOMContentLoaded', function () {
        $('#modalTambahBooking').on('shown.bs.modal', function () {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Koreksi ke waktu lokal

            const formatted = now.toISOString().slice(0, 16); // Format: yyyy-MM-ddTHH:mm
            document.getElementById('waktu_booking').value = formatted;
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('.btn-edit-user').click(function() {
            let id = $(this).data('id');
            let username = $(this).data('username');
            let role = $(this).data('role');

            $('#edit_user_id').val(id);
            $('#edit_username').val(username);
            $('#edit_role').val(role);

            $('#modalEditUser').modal('show');
        });
    });
    </script>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('tambah_password');
        const icon = this.querySelector('i');

        if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        }
    });
    </script>

</body>

</html>