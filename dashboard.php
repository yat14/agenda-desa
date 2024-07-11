<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
$peran = $_SESSION['peran'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Agenda Desa</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .sidebar {
            background-color: #343a40; /* Warna background navbar */
            height: 100vh;
            color: black;
        }
        .welcome-message {
            margin-top: 20px;
            margin-bottom: 20px;
            color: black;
            font-size: 1.5rem;
            font-weight: bold;
            transition: transform 0.3s ease, color 0.3s ease, text-shadow 0.3s ease; /* Animasi hover */
        }
        .welcome-message:hover {
            color: #28a745; /* Warna teks saat hover */
            transform: scale(2.05); /* Sedikit membesar saat hover */
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Bayangan teks saat hover */
        }
        .nav-link {
            font-size: 16px;
            padding: 10px 15px;
            color: black;
            text-decoration: none; /* Hilangkan garis bawah */
            border-radius: 8px; /* Radius tombol */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan halus */
        }
        .nav-link:hover {
            background-color: #495057;
            color: white; /* Hilangkan warna tulisan link di menu */
        }
        .nav-link.active {
            background-color: #28a745; /* Warna hijau */
            color: white;
        }
        .nav-link.active:hover {
            background-color: #218838; /* Warna hijau lebih gelap saat hover */
        }
        .nav-link i {
            margin-right: 10px;
        }
        .nav-link.text-danger {
            margin-top: 20px;
            color: white;
            background-color: #dc3545; /* Warna merah */
        }
        .nav-link.text-danger:hover {
            background-color: #c82333; /* Warna merah lebih gelap saat hover */
        }
        .content {
            padding: 20px;
        }
        .centered-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .nav-pills {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .nav-pills .nav-link {
            width: 100%;
            max-width: 200px;
            margin-bottom: 10px;
            background-color: #28a745; /* Warna hijau */
            color: white;
        }
        .nav-pills .nav-link:hover {
            background-color: #218838; /* Warna hijau lebih gelap saat hover */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4 centered-content">
                <div class="content">
                    <h2 class="welcome-message">Selamat Datang, <?php echo htmlspecialchars($user['nama_pengguna']); ?>!</h2>
                    <div class="nav nav-pills flex-column">
                        <?php if ($peran == 1) { // Admin ?>
                            <a class="nav-link active" href="agenda.php">
                                <i class="fas fa-calendar-alt"></i> Menu Admin
                            </a>
                        <?php } ?>
                        <?php if ($peran == 2) { // Kepala Desa ?>
                            <a class="nav-link" href="validasi_agenda.php">
                                <i class="fas fa-calendar-check"></i> Validasi Agenda
                            </a>
                        <?php } ?>
                        <?php if ($peran == 3) { // Warga ?>
                            <a class="nav-link" href="view_agenda.php">
                                <i class="fas fa-eye"></i> Lihat Agenda
                            </a>
                        <?php } ?>
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
