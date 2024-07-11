<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['peran'] != 1) {
    header("Location: index.php");
    exit();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id_pengguna = $_POST['id_pengguna'];
    $nama_pengguna = $_POST['nama_pengguna'];
    $kata_sandi = $_POST['kata_sandi'];
    $id_peran = $_POST['id_peran'];

    // Server-side validation
    if (empty($nama_pengguna) || empty($id_peran)) {
        $error_message = "Nama pengguna dan peran tidak boleh kosong.";
    } elseif (!empty($kata_sandi) && (strlen($kata_sandi) < 8 || !preg_match('/[A-Z]/', $kata_sandi) || !preg_match('/[a-z]/', $kata_sandi) || !preg_match('/[0-9]/', $kata_sandi))) {
        $error_message = "Kata sandi harus terdiri dari minimal 8 karakter, termasuk huruf besar, huruf kecil, dan angka.";
    } else {
        if (!empty($kata_sandi)) {
            $kata_sandi_hashed = password_hash($kata_sandi, PASSWORD_BCRYPT);
            $sql = "UPDATE Pengguna SET nama_pengguna = ?, kata_sandi = ?, id_peran = ? WHERE id_pengguna = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $nama_pengguna, $kata_sandi_hashed, $id_peran, $id_pengguna);
        } else {
            $sql = "UPDATE Pengguna SET nama_pengguna = ?, id_peran = ? WHERE id_pengguna = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $nama_pengguna, $id_peran, $id_pengguna);
        }
        $stmt->execute();
        $stmt->close();
        header('Location: users.php');
        exit;
    }
}

if (isset($_GET['id_pengguna'])) {
    $id_pengguna = $_GET['id_pengguna'];
    $fetch_sql = "SELECT * FROM Pengguna WHERE id_pengguna = ?";
    $stmt = $conn->prepare($fetch_sql);
    $stmt->bind_param("i", $id_pengguna);
    $stmt->execute();
    $result = $stmt->get_result();
    $pengguna = $result->fetch_assoc();
    $stmt->close();
} else {
    header('Location: users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .edit-user-container {
            max-width: 600px;
            margin: 50px auto;
        }
        .edit-user-container h1 {
            margin-bottom: 20px;
        }
        .btn-submit {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
    <script>
        function validateForm() {
            let namaPengguna = document.forms["editUserForm"]["nama_pengguna"].value;
            let kataSandi = document.forms["editUserForm"]["kata_sandi"].value;
            let idPeran = document.forms["editUserForm"]["id_peran"].value;
            let errorMessage = '';

            if (namaPengguna === '' || idPeran === '') {
                errorMessage = 'Nama pengguna dan peran tidak boleh kosong.';
            } else if (kataSandi !== '' && (kataSandi.length < 8 || !/[A-Z]/.test(kataSandi) || !/[a-z]/.test(kataSandi) || !/[0-9]/.test(kataSandi))) {
                errorMessage = 'Kata sandi harus terdiri dari minimal 8 karakter, termasuk huruf besar, huruf kecil, dan angka.';
            }

            if (errorMessage) {
                document.getElementById('error_message').innerText = errorMessage;
                return false;
            }

            return true;
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
                            <h1 class="m-0">Edit Pengguna</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card edit-user-container">
                        <div class="card-header">
                            <h3 class="card-title">Edit Pengguna</h3>
                        </div>
                        <div class="card-body">
                            <form name="editUserForm" method="POST" onsubmit="return validateForm()">
                                <input type="hidden" name="id_pengguna" value="<?php echo htmlspecialchars($pengguna['id_pengguna']); ?>">
                                <div class="form-group">
                                    <label for="nama_pengguna" class="form-label">Nama Pengguna</label>
                                    <input type="text" id="nama_pengguna" name="nama_pengguna" class="form-control" value="<?php echo htmlspecialchars($pengguna['nama_pengguna']); ?>" placeholder="Nama Pengguna" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="kata_sandi" class="form-label">Kata Sandi Baru</label>
                                    <input type="password" id="kata_sandi" name="kata_sandi" class="form-control" placeholder="Kata Sandi Baru">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="id_peran" class="form-label">Peran</label>
                                    <select id="id_peran" name="id_peran" class="form-control" required>
                                        <?php
                                        $peran_sql = "SELECT * FROM Peran";
                                        $peran_result = $conn->query($peran_sql);
                                        while ($row = $peran_result->fetch_assoc()) {
                                            $selected = ($row['id_peran'] == $pengguna['id_peran']) ? 'selected' : '';
                                            echo "<option value='".$row['id_peran']."' ".$selected.">".$row['nama_peran']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="update" class="btn btn-submit mt-4">Simpan Perubahan</button>
                                <a href="users.php" class="btn btn-cancel mt-4">Batalkan</a>
                            </form>
                            <div id="error_message" style="color: red;">
                                <?php if (isset($error_message)) echo $error_message; ?>
                            </div>
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

