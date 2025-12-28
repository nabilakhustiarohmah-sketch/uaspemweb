<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$query_arsip = mysqli_query($koneksi, "
    SELECT * FROM transaksi 
    WHERE user_id = '$user_id'
    ORDER BY tanggal DESC
");

$query_total_kumulatif = mysqli_query($koneksi, "
    SELECT 
        SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) AS total_masuk,
        SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) AS total_keluar
    FROM transaksi 
    WHERE user_id = '$user_id'
");
$data_total = mysqli_fetch_assoc($query_total_kumulatif);
$total_masuk = $data_total['total_masuk'] ?? 0;
$total_keluar = $data_total['total_keluar'] ?? 0;
$saldo_total = $total_masuk - $total_keluar;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap riwayat transaksi</title>
    <style>
        *{ margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f4f7f6; padding: 20px; }
        
        .container { max-width: 1000px; margin: auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; margin-bottom: 25px; }
        .btn-back { padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; }
        .btn-back:hover { background: #45a049; }

        .summary-grid { display: flex; gap: 15px; margin-bottom: 30px; }
        .card { flex: 1; padding: 20px; border-radius: 12px; color: white; text-align: center; }
        .card.masuk { background: #27ae60; }
        .card.keluar { background: #e74c3c; }
        .card.saldo { background: #2980b9; }
        .card h4 { font-size: 14px; margin-bottom: 10px; opacity: 0.9; }
        .card p { font-size: 20px; font-weight: bold; }

        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { padding: 12px; border: 1px solid #eee; text-align: center; }
        .table th { background-color: #f8f9fa; font-size: 14px; color: #333; }
        
        .pemasukan-row { background-color: #e8f8ee; color: #27ae60; font-weight: bold; }
        .pengeluaran-row { background-color: #fdecea; color: #e74c3c; font-weight: bold; }
        
        .badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; color: white; }
        .badge.masuk { background: #27ae60; }
        .badge.keluar { background: #e74c3c; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h2>Rekap Riwayat Transaksi</h2>
            <p style="color: #666; font-size: 14px;">Menampilkan semua data untuk: <b><?= ucfirst($username); ?></b></p>
        </div>
        <a href="dashboard.php" class="btn-back">⬅ Kembali ke Dashboard</a>
    </header>

    <div class="summary-grid">
        <div class="card masuk">
            <h4>Rekap Pemasukan </h4>
            <p>Rp <?= number_format($total_masuk, 0, ',', '.'); ?></p>
        </div>
        <div class="card keluar">
            <h4>Rekap Pengeluaran </h4>
            <p>Rp <?= number_format($total_keluar, 0, ',', '.'); ?></p>
        </div>
        <div class="card saldo">
            <h4>Saldo Akhir</h4>
            <p>Rp <?= number_format($saldo_total, 0, ',', '.'); ?></p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal </th>
                <th>Bulan</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_arsip)) { 
                $is_masuk = ($row['jenis'] == 'pemasukan');
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                <td><i style="color: #888;"><?= date('F Y', strtotime($row['tanggal'])); ?></i></td>
                <td>
                    <span class="badge <?= $is_masuk ? 'masuk' : 'keluar'; ?>">
                        <?= ucfirst($row['jenis']); ?>
                    </span>
                </td>
                <td style="text-align: left; padding-left: 20px;"><?= $row['keterangan']; ?></td>
                <td class="<?= $is_masuk ? 'pemasukan-row' : 'pengeluaran-row'; ?>">
                    <?= $is_masuk ? '(+)' : '(-)'; ?> Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?>
                </td>
                <td>
                    <a href="hapus_arsip.php?id=<?= $row['id']; ?>" 
                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" 
                       style="color: #e74c3c; text-decoration: none; font-weight: bold; font-size: 13px;">
                       Hapus
                    </a>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
</div>
</body>
</html> 