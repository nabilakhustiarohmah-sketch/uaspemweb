<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];


    $query = mysqli_query($koneksi, "DELETE FROM transaksi WHERE id = '$id' AND user_id = '$user_id'");

    if ($query) {
        echo "<script>
                alert('Data berhasil dihapus!');
                window.location='arsip.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data!');
                window.location='arsip.php';
              </script>";
    }
} else {
    header("Location: arsip.php");
}
?>