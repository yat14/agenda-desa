<?php
include('db.php');
session_start();
if ($_SESSION['peran'] != 3) {
    header('Location: index.php');
    exit;
}

// Mengambil agenda yang sudah divalidasi
$query = "SELECT * FROM agenda WHERE status = 'validated'";
$result = mysqli_query($conn, $query);

if (isset($_POST['konfirmasi'])) {
    $id_agenda = $_POST['id_agenda'];
    $id_pengguna = $_SESSION['user']['id_pengguna'];

    // Periksa apakah entri sudah ada
    $check_query = "SELECT * FROM konfirmasi_kehadiran WHERE id_agenda = $id_agenda AND id_pengguna = $id_pengguna";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        // Tambahkan entri baru
        $query = "INSERT INTO konfirmasi_kehadiran (id_agenda, id_pengguna) VALUES ($id_agenda, $id_pengguna)";
        mysqli_query($conn, $query);
        $_SESSION['message'] = "Konfirmasi kehadiran berhasil.";
    } else {
        $_SESSION['message'] = "Anda sudah mengonfirmasi kehadiran untuk agenda ini.";
    }
    header('Location: view_agenda.php');
    exit;
}

if (isset($_POST['batal_konfirmasi'])) {
    $id_agenda = $_POST['id_agenda'];
    $id_pengguna = $_SESSION['user']['id_pengguna'];

    // Hapus entri
    $query = "DELETE FROM konfirmasi_kehadiran WHERE id_agenda = $id_agenda AND id_pengguna = $id_pengguna";
    mysqli_query($conn, $query);
    $_SESSION['message'] = "Kehadiran telah dibatalkan.";
    header('Location: view_agenda.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Agenda</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .view-agenda-container {
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto;
            margin-top: 30px;
        }
        .view-agenda-container h1 {
            margin-bottom: 20px;
            text-align: center;
        }
        .alert {
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-confirm, .btn-cancel {
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .btn-confirm {
            background-color: #28a745;
        }
        .btn-confirm:hover {
            background-color: #218838;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-cancel {
            background-color: #dc3545;
        }
        .btn-cancel:hover {
            background-color: #c82333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .status-confirmed {
            color: #6c757d;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="dashboard.php" class="brand-link">
            <img src="assets/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
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
                        <h1 class="m-0">Lihat Agenda</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="view-agenda-container">
                    <h1>Lihat Agenda</h1>

                    <?php
                    if (isset($_SESSION['message'])) {
                        echo "<div class='alert alert-info'>" . $_SESSION['message'] . "</div>";
                        unset($_SESSION['message']);
                    }
                    ?>

                    <table class="table table-striped table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id_agenda']; ?></td>
                                <td><?php echo $row['nama_agenda']; ?></td>
                                <td><?php echo $row['deskripsi_agenda']; ?></td>
                                <td><?php echo $row['tanggal_agenda']; ?></td>
                                <td>
                                    <?php
                                    $id_agenda = $row['id_agenda'];
                                    $id_pengguna = $_SESSION['user']['id_pengguna'];
                                    $check_query = "SELECT * FROM konfirmasi_kehadiran WHERE id_agenda = $id_agenda AND id_pengguna = $id_pengguna";
                                    $check_result = mysqli_query($conn, $check_query);

                                    if (mysqli_num_rows($check_result) == 0) { ?>
                                        <form method="post" action="view_agenda.php">
                                            <input type="hidden" name="id_agenda" value="<?php echo $row['id_agenda']; ?>">
                                            <button type="submit" name="konfirmasi" class="btn-confirm">Konfirmasi Kehadiran</button>
                                        </form>
                                    <?php } else { ?>
                                        <form method="post" action="view_agenda.php">
                                            <input type="hidden" name="id_agenda" value="<?php echo $row['id_agenda']; ?>">
                                            <button type="submit" name="batal_konfirmasi" class="btn-cancel">Batalkan Kehadiran</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>
