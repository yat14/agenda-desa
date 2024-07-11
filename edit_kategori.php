<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['peran'] != 1) {
    header("Location: index.php");
    exit();
}

require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM kategori_agenda WHERE id_kategori = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_kategori = $_POST['nama_kategori'];

    if (!empty($nama_kategori)) {
        $stmt = $conn->prepare("UPDATE kategori_agenda SET nama_kategori = ? WHERE id_kategori = ?");
        $stmt->bind_param("si", $nama_kategori, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: kategori_agenda.php");
        exit();
    } else {
        $error = "Nama kategori tidak boleh kosong.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori Agenda</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            max-width: 500px;
            width: 100%;
        }
        .card-header {
            background-color: #28a745; /* Warna hijau */
            color: #ffffff;
        }
        .card-body label {
            font-weight: bold;
        }
        .card-body input[type="text"] {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #28a745; /* Warna hijau */
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838; /* Warna hijau lebih gelap */
        }
        .btn-secondary {
            background-color: #6c757d; /* Warna abu-abu */
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268; /* Warna abu-abu lebih gelap */
        }
        .alert-danger {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Kategori Agenda</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id_kategori']); ?>">
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" value="<?php echo htmlspecialchars($category['nama_kategori']); ?>" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Update Kategori</button>
                    <a href="kategori_agenda.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>
