<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $bulan = $_GET['bulan'] ?? date('m');

    mysqli_query($koneksi, "DELETE FROM transaksi WHERE id='$id'");
}
header("Location: dashboard.php?bulan=$bulan");
exit;
