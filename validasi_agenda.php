<?php
// Check if the user is Kepala Desa
session_start();
if ($_SESSION['peran'] != 2) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];

// Database connection
include('db.php');

// Fetch pending agenda items
$query_pending = "SELECT * FROM agenda WHERE status = 'pending'";
$result_pending = mysqli_query($conn, $query_pending);

// Fetch validated agenda items
$query_validated = "SELECT * FROM agenda WHERE status = 'validated'";
$result_validated = mysqli_query($conn, $query_validated);

if (isset($_POST['validate'])) {
    $id_agenda = $_POST['id_agenda'];
    $query = "UPDATE agenda SET status = 'validated' WHERE id_agenda = $id_agenda";
    mysqli_query($conn, $query);
    header('Location: validasi_agenda.php');
    exit;
}

if (isset($_POST['invalidate'])) {
    $id_agenda = $_POST['id_agenda'];
    $query = "UPDATE agenda SET status = 'pending' WHERE id_agenda = $id_agenda";
    mysqli_query($conn, $query);
    header('Location: validasi_agenda.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Agenda</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa; /* Latar belakang halaman */
            height: 110vh;
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
        .card-title {
            text-align: center;
        }
        .btn-validate {
            background-color: #28a745; /* Hijau untuk tombol Validasi */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan halus */
        }
        .btn-validate:hover {
            background-color: #218838; /* Hijau lebih gelap saat hover */
        }
        .btn-invalidate {
            background-color: #dc3545; /* Merah untuk tombol Batalkan Validasi */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan halus */
        }
        .btn-invalidate:hover {
            background-color: #c82333; /* Merah lebih gelap saat hover */
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
                            <a href="logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                        <!-- Add more menu items as needed -->
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Validasi Agenda</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Card for Pending Agenda -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Agenda Menunggu Validasi</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                                    <tr>
                                        <td><?php echo $row['id_agenda']; ?></td>
                                        <td><?php echo $row['nama_agenda']; ?></td>
                                        <td><?php echo $row['deskripsi_agenda']; ?></td>
                                        <td>
                                            <form method="post" action="validasi_agenda.php">
                                                <input type="hidden" name="id_agenda" value="<?php echo $row['id_agenda']; ?>">
                                                <button type="submit" name="validate" class="btn btn-validate">Validasi</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Card for Validated Agenda -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Agenda Sudah Divalidasi</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result_validated)) { ?>
                                    <tr>
                                        <td><?php echo $row['id_agenda']; ?></td>
                                        <td><?php echo $row['nama_agenda']; ?></td>
                                        <td><?php echo $row['deskripsi_agenda']; ?></td>
                                        <td>
                                            <form method="post" action="validasi_agenda.php">
                                                <input type="hidden" name="id_agenda" value="<?php echo $row['id_agenda']; ?>">
                                                <button type="submit" name="invalidate" class="btn btn-invalidate">Batalkan Validasi</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div><!-- ./wrapper -->

    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
