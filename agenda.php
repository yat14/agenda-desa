<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['peran'] != 1 && $_SESSION['peran'] != 2)) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
include 'db.php';

// Handling agenda deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id_agenda = $_POST['id_agenda'];
    $stmt = $conn->prepare("DELETE FROM agenda WHERE id_agenda = ?");
    $stmt->bind_param("i", $id_agenda);
    if ($stmt->execute()) {
        // Successful deletion
        $stmt->close();
        header("Location: agenda.php");
        exit();
    } else {
        // Handle error if needed
        $stmt->close();
    }
}

// Handling agenda addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_agenda'])) {
    $nama_agenda = $_POST['nama_agenda'];
    $tanggal_agenda = $_POST['tanggal_agenda'];
    $deskripsi_agenda = $_POST['deskripsi_agenda'];
    $id_kategori = $_POST['id_kategori'];

    $stmt = $conn->prepare("INSERT INTO agenda (nama_agenda, tanggal_agenda, deskripsi_agenda, id_kategori) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nama_agenda, $tanggal_agenda, $deskripsi_agenda, $id_kategori);
    $stmt->execute();
    $stmt->close();
}

// Fetch all agendas
$sql = "SELECT * FROM agenda";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Agenda</title>
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
            background-color: #f8f9fa; /* Latar belakang halaman */
            height: 100vh;
        }
        .agenda-container {
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 30px auto;
        }
        .agenda-container h1 {
            margin-bottom: 20px;
            text-align: center;
        }
        .agenda-container h2 {
            margin-top: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        .agenda-container form input, 
        .agenda-container form textarea, 
        .agenda-container form select {
            margin-bottom: 10px;
        }
        .agenda-container form textarea {
            height: 100px;
        }
        .agenda-container form button {
            width: 100%;
        }
        .btn-add-agenda {
            background-color: #007bff; /* Biru untuk tombol Tambah Agenda */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-add-agenda:hover {
            background-color: #0056b3; /* Biru lebih gelap saat hover */
        }
        .table thead th {
            background-color: green; /* Warna background header tabel */
            color: #ffffff; /* Warna teks header tabel */
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Warna latar belakang baris genap */
        }
        .table-bordered {
            border: 2px solid #000000; /* Border tabel berwarna hitam */
        }
        .table-bordered th, .table-bordered td {
            border: 2px solid #000000; /* Garis tebal untuk sel tabel */
        }
        .btn-edit, .btn-delete {
            margin: 0 5px;
        }
        .btn-edit {
            background-color: #ffc107; /* Kuning untuk tombol Edit */
            color: #000000;
        }
        .btn-edit:hover {
            background-color: #e0a800; /* Kuning lebih gelap saat hover */
        }
        .btn-delete {
            background-color: #dc3545; /* Merah untuk tombol Hapus */
            color: #ffffff;
        }
        .btn-delete:hover {
            background-color: #c82333; /* Merah lebih gelap saat hover */
        }
        .btn-back {
            background-color: #6c757d; /* Abu-abu untuk tombol Kembali */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-back:hover {
            background-color: #5a6268; /* Abu-abu lebih gelap saat hover */
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
        .form-label {
            font-weight: bold;
        }
        .brand-text {
            font-size: 16px; /* Mengubah ukuran font */
            font-weight: normal; /* Mengubah berat font */
        }
    </style>
    <script>
        function confirmDelete(form) {
            if (confirm("Apakah Anda yakin ingin menghapus agenda ini?")) {
                form.submit();
            }
        }
    </script>
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
                        <!-- Add more menu items as needed -->
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
                            <h1 class="m-0">Manajemen Agenda</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="agenda-container">
                        <h2 class="mt-2">Daftar Agenda</h2>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                    // Fetch the category name
                                    $kategori_id = $row['id_kategori'];
                                    $kategori_name_query = $conn->prepare("SELECT nama_kategori FROM kategori_agenda WHERE id_kategori = ?");
                                    $kategori_name_query->bind_param("i", $kategori_id);
                                    $kategori_name_query->execute();
                                    $kategori_name_result = $kategori_name_query->get_result();
                                    $kategori_name = $kategori_name_result->fetch_assoc()['nama_kategori'];
                                    $kategori_name_query->close();
                                ?>
                                <tr>
                                    <td><?php echo $row['id_agenda']; ?></td>
                                    <td><?php echo $row['nama_agenda']; ?></td>
                                    <td><?php echo $row['tanggal_agenda']; ?></td>
                                    <td><?php echo $row['deskripsi_agenda']; ?></td>
                                    <td><?php echo $kategori_name; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href='edit_agenda.php?id_agenda=<?php echo $row['id_agenda']; ?>' class='btn btn-warning'><i class='fas fa-edit'></i></a>
                                            <form method='POST' style='display: inline;' onsubmit='confirmDelete(this);'>
                                                <input type='hidden' name='id_agenda' value='<?php echo $row['id_agenda']; ?>'>
                                                <button type='submit' name='delete' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama_agenda" class="form-label">Nama Agenda</label>
                                <input type="text" id="nama_agenda" name="nama_agenda" class="form-control" placeholder="Masukkan Nama Agenda" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_agenda" class="form-label">Tanggal Agenda</label>
                                <input type="date" id="tanggal_agenda" name="tanggal_agenda" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi_agenda" class="form-label">Deskripsi Agenda</label>
                                <textarea id="deskripsi_agenda" name="deskripsi_agenda" class="form-control" placeholder="Masukkan Deskripsi Agenda" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="id_kategori" class="form-label">Kategori Agenda</label>
                                <select id="id_kategori" name="id_kategori" class="form-select" required>
                                    <?php
                                    $kategori_sql = "SELECT * FROM kategori_agenda";
                                    $kategori_result = $conn->query($kategori_sql);
                                    while ($row = $kategori_result->fetch_assoc()) {
                                        echo "<option value='".$row['id_kategori']."'>".$row['nama_kategori']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="add_agenda" class="btn btn-success">Tambah Agenda</button>
                        </form>
                    </div>
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
