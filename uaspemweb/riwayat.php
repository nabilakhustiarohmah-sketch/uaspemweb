<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$bulan =$_GET['bulan'] ?? date('m');

// Query pemasukan
$pemasukan = mysqli_query($koneksi, "
    SELECT * FROM transaksi
    WHERE user_id = '$user_id' AND jenis = 'pemasukan'
    ORDER BY tanggal DESC, id DESC
");

// Query pengeluaran
$pengeluaran = mysqli_query($koneksi, "
    SELECT * FROM transaksi
    WHERE user_id = '$user_id' AND jenis = 'pengeluaran'
    ORDER BY tanggal DESC, id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Transaksi</title>
</head>
<body>

<h2>📄 Rekap Transaksi</h2>

<a href="dashboard.php">⬅ Kembali ke Dashboard</a>
<br><br>

<h3 style="color:green;">✔ Daftar Pemasukan</h3>

<table border="1" cellpadding="10" cellspacing="0">
    <tr style="background:#d4ffd4; font-weight:bold;">
        <th>No</th>
        <th>Jumlah (Rp)</th>
        <th>Keterangan</th>
        <th>Tanggal</th>
    </tr>

    <?php
    $no = 1;
    if (mysqli_num_rows($pemasukan) > 0) {
        while ($row = mysqli_fetch_assoc($pemasukan)) {
    ?>
        <tr>
            <td><?= $no++; ?></td>
            <td>Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
            <td><?= $row['keterangan']; ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
        </tr>
    <?php
        }
    } else {
    ?>
        <tr>
            <td colspan="4" style="text-align:center; color:red;">
                Belum ada pemasukan.
            </td>
        </tr>
    <?php } ?>
</table>

<br><br>

<h3 style="color:red;">✖ Daftar Pengeluaran</h3>

<table border="1" cellpadding="10" cellspacing="0">
    <tr style="background:#ffd4d4; font-weight:bold;">
        <th>No</th>
        <th>Jumlah (Rp)</th>
        <th>Keterangan</th>
        <th>Tanggal</th>
    </tr>

    <?php
    $no = 1;
    if (mysqli_num_rows($pengeluaran) > 0) {
        while ($row = mysqli_fetch_assoc($pengeluaran)) {
    ?>
        <tr>
            <td><?= $no++; ?></td>
            <td>Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
            <td><?= $row['keterangan']; ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
        </tr>
    <?php
        }
    } else { 
    ?>
        <tr>
            <td colspan="4" style="text-align:center; color:red;">
                Belum ada pengeluaran.
            </td>
        </tr>
    <?php } ?>
</table>
 <input type="hidden" name="bulan" value="<?= $bulan; ?>">
 <br>
<a href="dashboard.php?bulan=<?= $bulan; ?>">⬅ Kembali ke Dashboard</a>
</br>
</body>
</html>
