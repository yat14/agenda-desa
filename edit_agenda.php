<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['peran'] != 1 && $_SESSION['peran'] != 2)) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
include 'db.php';

if (isset($_GET['id_agenda'])) {
    $id_agenda = $_GET['id_agenda'];
    $stmt = $conn->prepare("SELECT * FROM agenda WHERE id_agenda = ?");
    $stmt->bind_param("i", $id_agenda);
    $stmt->execute();
    $result = $stmt->get_result();
    $agenda = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agenda = $_POST['id_agenda'];
    $nama_agenda = $_POST['nama_agenda'];
    $tanggal_agenda = $_POST['tanggal_agenda'];
    $deskripsi_agenda = $_POST['deskripsi_agenda'];
    $id_kategori = $_POST['id_kategori'];

    $stmt = $conn->prepare("UPDATE Agenda SET nama_agenda = ?, tanggal_agenda = ?, deskripsi_agenda = ?, id_kategori = ? WHERE id_agenda = ?");
    $stmt->bind_param("sssii", $nama_agenda, $tanggal_agenda, $deskripsi_agenda, $id_kategori, $id_agenda);
    $stmt->execute();
    $stmt->close();
    header("Location: agenda.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Agenda</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa; /* Latar belakang halaman */
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1;
        }
        .main-footer {
            margin-top: 0; /* Menghilangkan jarak di atas footer */
            padding-top: 10px; /* Menyesuaikan jarak di atas footer */
            padding-bottom: 0; /* Menghilangkan jarak di bawah footer */
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="dashboard.php" class="brand-link">
                <img src="assets/img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Hai! <?php echo htmlspecialchars($user['nama_pengguna']); ?></span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Kembali ke Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="agenda.php" class="nav-link active">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Manajemen Agenda</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="kategori_agenda.php" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Kategori Agenda</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="users.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Pengelolaan Pengguna</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="laporan_warga.php" class="nav-link">
                                <i class="nav-icon fa fa-address-book"></i>
                                <p>Laporan kehadiran</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Agenda</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Edit Agenda</h3>
                        </div>
                        <!-- form start -->
                        <form method="POST">
                            <div class="card-body">
                                <input type="hidden" name="id_agenda" value="<?php echo htmlspecialchars($agenda['id_agenda']); ?>">
                                <div class="form-group">
                                    <label for="nama_agenda">Nama Agenda</label>
                                    <input type="text" class="form-control" id="nama_agenda" name="nama_agenda" value="<?php echo htmlspecialchars($agenda['nama_agenda']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_agenda">Tanggal Agenda</label>
                                    <input type="date" class="form-control" id="tanggal_agenda" name="tanggal_agenda" value="<?php echo htmlspecialchars($agenda['tanggal_agenda']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi_agenda">Deskripsi Agenda</label>
                                    <textarea class="form-control" id="deskripsi_agenda" name="deskripsi_agenda" required><?php echo htmlspecialchars($agenda['deskripsi_agenda']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="id_kategori">Kategori</label>
                                    <select class="form-control" id="id_kategori" name="id_kategori" required>
                                        <?php
                                        $kategori_sql = "SELECT * FROM Kategori_Agenda";
                                        $kategori_result = $conn->query($kategori_sql);
                                        while ($row = $kategori_result->fetch_assoc()) {
                                            echo "<option value='".$row['id_kategori']."' ".($row['id_kategori'] == $agenda['id_kategori'] ? 'selected' : '').">".$row['nama_kategori']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="agenda.php" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Anything you want
            </div>
            <strong>Copyright &copy; 2024 <a href="#">Company</a>.</strong> All rights reserved.
        </footer>
    </div><!-- ./wrapper -->

    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
