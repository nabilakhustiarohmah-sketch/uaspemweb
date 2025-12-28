<?php
session_start();
include'koneksi.php';

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {

        if (password_verify($password, $data['password'])) {

            $_SESSION['user_id'] = $data['id'];
            $_SESSION['username'] = $data['username'];

        if (isset($_SESSION['user_id'])){
            header("Location: dashboard.php");
        }  

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Akun</title>
    <link rel="stylesheet" href="style_login.css?v=1.2">

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
<h2>Login</h2>

    <?php if (!empty($error)) { ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php } ?>

    <?php if (!empty($success)) { ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php } ?>

<form method="POST">

    <div class ="inputBox">
        <input type="text" name="username" placeholder="username" required>
    </div>

    <div class ="inputBox">
        <input type="text" name="password" placeholder="password" required>
    </div>

    <button type="submit" name="login">Login</button>
</form>

<p>Belum punya akun? <a href="register.php">Register</a></p>
    </div>
</body>
</html>

