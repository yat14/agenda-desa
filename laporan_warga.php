<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['peran'] != 1) {
    header("Location: index.php");
    exit();
}
include 'db.php';

// Query untuk mengambil data warga yang sudah konfirmasi kehadiran beserta informasi agenda dan tanggal konfirmasi
$query_confirmed = "SELECT konfirmasi_kehadiran.*, pengguna.nama_pengguna, agenda.nama_agenda, konfirmasi_kehadiran.tanggal_konfirmasi
                    FROM konfirmasi_kehadiran
                    INNER JOIN pengguna ON konfirmasi_kehadiran.id_pengguna = pengguna.id_pengguna
                    INNER JOIN agenda ON konfirmasi_kehadiran.id_agenda = agenda.id_agenda";
$result_confirmed = mysqli_query($conn, $query_confirmed);

// Query untuk mengambil data warga yang belum konfirmasi kehadiran
$query_unconfirmed = "SELECT pengguna.nama_pengguna 
                      FROM pengguna 
                      INNER JOIN peran ON pengguna.id_peran = peran.id_peran 
                      WHERE pengguna.id_pengguna NOT IN (SELECT id_pengguna FROM konfirmasi_kehadiran) 
                      AND peran.nama_peran = 'warga'";
$result_unconfirmed = mysqli_query($conn, $query_unconfirmed);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aktivitas Warga</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 30px auto;
        }
        h2 {
            margin-bottom: 30px;
            text-align: center;
        }
        h3 {
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .download-buttons {
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-submit {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 5px;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .table thead th {
            background-color: #007bff;
            color: #ffffff;
            border-bottom: 2px solid #0056b3;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
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
                <span class="brand-text font-weight-light">Hai! <?php echo htmlspecialchars($_SESSION['user']['nama_pengguna']); ?></span>
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
                            <a href="agenda.php" class="nav-link">
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
                            <a href="laporan_warga.php" class="nav-link active">
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
                            <h1 class="m-0">Laporan Aktivitas Warga</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rekap Kehadiran Warga</h3>
                        </div>
                        <div class="card-body">

                            <!-- Tombol untuk mengunduh rekap hasil kehadiran dalam berbagai format -->
                            <div class="download-buttons mb-4">
                                <a href="download_laporan.php?format=csv" class="btn btn-submit">Unduh CSV</a>
                                <a href="download_laporan.php?format=word" class="btn btn-submit">Unduh Word</a>
                            </div>
                            
                            <h3>Warga yang Sudah Konfirmasi Kehadiran</h3>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Pengguna</th>
                                        <th>Agenda</th>
                                        <th>Waktu Konfirmasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result_confirmed)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_agenda']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_konfirmasi']); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <h3>Warga yang Belum Konfirmasi Kehadiran</h3>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Pengguna</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result_unconfirmed)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5.3 JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>
