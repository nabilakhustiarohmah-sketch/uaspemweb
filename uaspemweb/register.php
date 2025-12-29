<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'koneksi.php';

if (isset($_POST['register'])) {

    $username  = $_POST['username'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password !== $password2) {
        $error = "Password dan konfirmasi tidak cocok!";
    } else {
        $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah digunakan.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = mysqli_query($koneksi, "
                INSERT INTO users (username, email, password)
                VALUES ('$username', '$email', '$password_hash')
            ");
            if ($query) {
                $success = "Akun berhasil dibuat. Silakan login!";
            } else {
                $error = "Terjadi kesalahan!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style_login.css?v=1.4">

<style>
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
    }

    .header-utama {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #4CAF50; 
        color: white;
        padding: 30px 0;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .header-utama h1 {
        font-family: 'Arial', sans-serif;
        font-size: 28px;
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
</style>

<header class="header-utama">
    <h1>Kalkulator Keuangan Mahasiswa</h1>
</header>
</head>

<body>

<div class="container">

    <h2>Daftar Akun</h2>

    <?php if (!empty($error)) { ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php } ?>

    <?php if (!empty($success)) { ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php } ?>

    <form method="POST">

        <div class="inputBox">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="inputBox">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="inputBox">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="inputBox">
            <input type="password" name="password2" placeholder="Konfirmasi Password" required>
        </div>

        <button type="submit" name="register">Daftar</button>

        <div class="links">
            <a href="login.php">Login</a>
        </div>

    </form>

</div>

</body>
</html>
