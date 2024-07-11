<?php
session_start();
include 'db.php';

$max_attempts = 5;  // Maksimal percobaan login
$lockout_time = 10 * 60;  // Waktu terkunci (10 menit)

// Cek jumlah percobaan login dan waktu terkunci
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    $last_attempt_time = $_SESSION['last_attempt_time'];
    if (time() - $last_attempt_time < $lockout_time) {
        $error = "Terlalu banyak percobaan login. Silakan coba lagi nanti.";
    } else {
        // Reset percobaan login setelah waktu terkunci
        unset($_SESSION['login_attempts']);
        unset($_SESSION['last_attempt_time']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $captcha_result = htmlspecialchars(trim($_POST['captcha_result']));
    $captcha_answer = $_SESSION['captcha_answer'];
    
    if ($captcha_result != $captcha_answer) {
        $error = "Hasil CAPTCHA salah!";
    } else if (strlen($password) < 8) {
        $error = "Kata sandi harus terdiri dari minimal 8 karakter!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM Pengguna WHERE nama_pengguna = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['kata_sandi'])) {
                $_SESSION['user'] = $row;
                $_SESSION['peran'] = $row['id_peran'];
                unset($_SESSION['login_attempts']);  // Reset percobaan login jika berhasil
                unset($_SESSION['last_attempt_time']);  // Reset waktu jika berhasil
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Password salah!";
                // Tambah percobaan login
                if (!isset($_SESSION['login_attempts'])) {
                    $_SESSION['login_attempts'] = 0;
                }
                $_SESSION['login_attempts'] += 1;
                $_SESSION['last_attempt_time'] = time();
            }
        } else {
            $error = "Pengguna tidak ditemukan!";
            // Tambah percobaan login
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            $_SESSION['login_attempts'] += 1;
            $_SESSION['last_attempt_time'] = time();
        }

        $stmt->close();
    }
}

// Generate CAPTCHA
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$_SESSION['captcha_answer'] = $num1 + $num2;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Agenda Desa</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Keamanan CSS dan penyesuaian */
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .bg {
            background: url('assets/img/ilovebks.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100%;
            filter: blur(8px);
            -webkit-filter: blur(8px);
            position: absolute;
            width: 100%;
            z-index: -1;
        }
        .container {
            position: relative;
            z-index: 1;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9); /* Adjusted transparency */
            border-radius: 15px; /* More rounded corners */
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
        .btn-primary {
            background-color: #28a745; /* Green background */
            border-color: #28a745; /* Green border */
        }
        .btn-primary:hover {
            background-color: #218838; /* Darker green background on hover */
            border-color: #1e7e34; /* Darker green border on hover */
        }
    </style>
</head>
<body>
    <div class="bg"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="text-center">Agenda Desa Kelapapati Darat</h2>
                        <img src="assets/img/logo.png" alt="Logo" class="img-fluid mt-3 d-block mx-auto" style="max-width: 75px;">
                    </div>
                    <div class="card-body">
                        <form id="loginForm" method="POST" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="username">Nama Pengguna</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Nama Pengguna" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Kata Sandi</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Kata Sandi" required>
                            </div>
                            <div class="form-group">
                                <label for="captcha">Hasil Penjumlahan</label>
                                <p><?php echo "$num1 + $num2 = ?"; ?></p>
                                <input type="text" class="form-control" id="captcha" name="captcha_result" placeholder="Masukkan hasil" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                            <?php if (isset($error)) echo "<p class='text-danger mt-3 text-center'>$error</p>"; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function validateForm() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var captcha = document.getElementById('captcha').value;
            if (username == "" || password == "" || captcha == "") {
                alert("Harap isi semua kolom.");
                return false;
            }
            if (password.length < 8) {
                alert("Kata sandi harus terdiri dari minimal 8 karakter.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
