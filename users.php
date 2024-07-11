<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['peran'] != 1 && $_SESSION['peran'] != 2)) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
include 'db.php';

// Handling user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id_pengguna = $_POST['id_pengguna'];
    $delete_sql = "DELETE FROM Pengguna WHERE id_pengguna = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id_pengguna);
    $stmt->execute();
    $stmt->close();
}

// Handling user addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $nama_pengguna = $_POST['nama_pengguna'];
    $kata_sandi = $_POST['kata_sandi'];
    $id_peran = $_POST['id_peran'];

    // Server-side validation
    if (strlen($kata_sandi) < 8 || !preg_match('/[A-Z]/', $kata_sandi) || !preg_match('/[a-z]/', $kata_sandi) || !preg_match('/[0-9]/', $kata_sandi)) {
        $error_message = "Kata sandi harus terdiri dari minimal 8 karakter, termasuk huruf besar, huruf kecil, dan angka.";
    } elseif (empty($nama_pengguna) || empty($kata_sandi) || empty($id_peran)) {
        $error_message = "Semua kolom harus diisi.";
    } else {
        $kata_sandi_hashed = password_hash($kata_sandi, PASSWORD_BCRYPT); // Encrypting password
        
        $add_sql = "INSERT INTO Pengguna (nama_pengguna, kata_sandi, id_peran) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($add_sql);
        $stmt->bind_param("ssi", $nama_pengguna, $kata_sandi_hashed, $id_peran);
        $stmt->execute();
        $stmt->close();
        $success_message = "Pengguna berhasil ditambahkan.";
    }
}

$sql = "SELECT * FROM Pengguna";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelolaan Pengguna</title>
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
        .content-wrapper {
            background-color: #ffffff; /* Background konten putih */
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff; /* Biru untuk header kartu */
            color: #ffffff;
        }
        .card-body form input, 
        .card-body form select {
            margin-bottom: 10px;
        }
        .btn-add-user {
            background-color: #007bff; /* Biru untuk tombol Tambah Pengguna */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-add-user:hover {
            background-color: #0056b3; /* Biru lebih gelap saat hover */
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
        function validateForm() {
            let namaPengguna = document.forms["userForm"]["nama_pengguna"].value;
            let kataSandi = document.forms["userForm"]["kata_sandi"].value;
            let idPeran = document.forms["userForm"]["id_peran"].value;
            let errorMessage = '';

            if (namaPengguna === '' || kataSandi === '' || idPeran === '') {
                errorMessage = 'Semua kolom harus diisi.';
            } else if (kataSandi.length < 8 || !/[A-Z]/.test(kataSandi) || !/[a-z]/.test(kataSandi) || !/[0-9]/.test(kataSandi)) {
                errorMessage = 'Kata sandi harus terdiri dari minimal 8 karakter, termasuk huruf besar, huruf kecil, dan angka.';
            }

            if (errorMessage) {
                document.getElementById('error_message').innerText = errorMessage;
                return false;
            }

            return true;
        }

        function confirmDeletion() {
            return confirm("Apakah Anda yakin ingin menghapus pengguna ini?");
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
                            <a href="users.php" class="nav-link active">
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
                            <h1 class="m-0">Pengelolaan Pengguna</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Pengguna</h3>
                        </div>
                        <div class="card-body">
                            <form name="userForm" method="POST" onsubmit="return validateForm()">
                                <div class="form-group">
                                    <input type="text" name="nama_pengguna" class="form-control" placeholder="Nama Pengguna" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="kata_sandi" class="form-control" placeholder="Kata Sandi" required>
                                </div>
                                <div class="form-group">
                                    <select name="id_peran" class="form-control" required>
                                        <option value="">Pilih Peran</option>
                                        <?php
                                        $roles = ["1" => "Admin", "2" => "Kepala Desa", "3" => "Warga"];
                                        foreach ($roles as $id => $role) {
                                            echo "<option value='$id'>$role</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="add_user" class="btn btn-add-user">Tambah Pengguna</button>
                            </form>
                            <div id="error_message" style="color: red;">
                                <?php if (isset($error_message)) echo $error_message; ?>
                            </div>
                            <div id="success_message" style="color: green;">
                                <?php if (isset($success_message)) echo $success_message; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Tabel Pengguna -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Pengguna</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Pengguna</th>
                                        <th>Nama Pengguna</th>
                                        <th>Peran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_pengguna']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td>
                                        <td><?php echo ($row['id_peran'] == 1) ? 'Admin' : (($row['id_peran'] == 2) ? 'Kepala Desa' : 'Warga'); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_pengguna.php?id_pengguna=<?php echo $row['id_pengguna']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirmDeletion();">
                                                    <input type="hidden" name="id_pengguna" value="<?php echo $row['id_pengguna']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-delete"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5.3 JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>
