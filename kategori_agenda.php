<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['peran'] != 1) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
require 'db.php'; // Include your database connection file

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = $_POST['nama_kategori'];

    if (!empty($nama_kategori)) {
        $stmt = $conn->prepare("INSERT INTO kategori_agenda (nama_kategori) VALUES (?)");
        $stmt->bind_param("s", $nama_kategori);
        $stmt->execute();
        $stmt->close();
        $success = "Kategori berhasil ditambahkan.";
    } else {
        $error = "Nama kategori tidak boleh kosong.";
    }
}

// Fetch all categories
$result = $conn->query("SELECT * FROM kategori_agenda");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori Agenda</title>
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
            background-color: #f8f9fa;
            height: 100vh;
            font-size: 0.9rem; /* Mengubah ukuran font seluruh halaman */
        }
        .form-label {
            font-weight: bold;
        }
        .brand-text {
            font-size: 0.9rem; /* Mengubah ukuran font */
            font-weight: normal;
        }
        table {
            font-size: 0.85rem; /* Mengubah ukuran font tabel */
        }
        .table th, .table td {
            padding: 0.5rem; /* Mengurangi padding tabel */
        }
        .sidebar .nav-link {
            font-size: 0.9rem; /* Mengubah ukuran font sidebar */
        }
        .welcome-message {
            font-size: 0.9rem; /* Mengubah ukuran font pesan selamat datang */
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="agenda.php" class="brand-link">
                <img src="assets/img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <h2 class="welcome-message">Hai! <?php echo htmlspecialchars($user['nama_pengguna']); ?></h2>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="agenda.php" class="nav-link">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Manajemen Agenda</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="kategori_agenda.php" class="nav-link active">
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
                                <p>Laporan Kehadiran</p>
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
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Manajemen Kategori Agenda</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Kategori Agenda</h3>
                        </div>
                        <!-- form start -->
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nama_kategori">Nama Kategori</label>
                                    <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" placeholder="Masukkan Nama Kategori" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Tambah Kategori</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                    <h2 class="mt-4">Daftar Kategori</h2>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['id_kategori']); ?></td>
                                    <td><?php echo htmlspecialchars($category['nama_kategori']); ?></td>
                                    <td>
                                        <a href="edit_kategori.php?id=<?php echo $category['id_kategori']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="delete_kategori.php?id=<?php echo $category['id_kategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')"><i class="fas fa-trash-alt"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- /.container-fluid -->
            </div><!-- /.content -->
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->

    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
