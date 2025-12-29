<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Query Pemasukan
$query_pemasukan = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(jumlah), 0) AS total_pemasukan
    FROM transaksi
    WHERE user_id = '$user_id'
    AND jenis = 'pemasukan'
    AND MONTH(tanggal) = '$bulan'
    AND YEAR(tanggal) = '$tahun'
");

// Query Pengeluaran
$query_pengeluaran = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(jumlah), 0) AS total_pengeluaran
    FROM transaksi
    WHERE user_id = '$user_id'
    AND jenis = 'pengeluaran'
    AND MONTH(tanggal) = '$bulan'
    AND YEAR(tanggal) = '$tahun'
");

$res_pemasukan = mysqli_fetch_assoc($query_pemasukan);
$res_pengeluaran = mysqli_fetch_assoc($query_pengeluaran);

$total_pemasukan = $res_pemasukan['total_pemasukan'];
$total_pengeluaran = $res_pengeluaran['total_pengeluaran'];
$saldo = $total_pemasukan - $total_pengeluaran;

// Query Daftar Transaksi
$query = mysqli_query($koneksi, "
    SELECT * FROM transaksi 
    WHERE user_id = '$user_id'
    AND MONTH(tanggal) = '$bulan'
    AND YEAR(tanggal) = '$tahun'
    ORDER BY tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Keuangan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        header { background-color: #4CAF50; color: white; padding: 20px 0; text-align: center; margin-bottom: 20px; }
        .header-container { display: flex; justify-content: space-between; align-items: center; padding: 0 20px; }
        .user-info { display: flex; flex-direction: column; align-items: flex-end; }
        .logout a { color: white; font-size: 14px; text-decoration: underline; }
        
        .container { width: 90%; max-width: 1000px; margin: auto; }
        .box-container { display: flex; gap: 20px; margin-bottom: 15px; justify-content: center; }
        .box { width: 220px; padding: 15px; border-radius: 15px; color: white; text-align: center; }
        .box.income { background: green; }
        .box.outcome { background: red; }
        .box.saldo { background: blue; }

        .action-btn { margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; color: white; text-decoration: none; font-weight: bold; }
        .btn.income { background: green; margin-right: 10px; }
        .btn.outcome { background: red; }
        .btn.archive { background: orange; margin-top: 10px; display: block; text-align: center; }

        .table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        tr.pemasukan { background-color: #e8f8ee; }
        tr.pengeluaran { background-color: #fdecea; }

        /* Style wadah diagram di bawah */
        .chart-container { width: 100%; max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<header>
  <div class="header-container">
    <h2>Kalkulator Keuangan Mahasiswa</h2>
    <div class="user-info">
        <span class="role">👩‍🎓 Mahasiswa</span>
        <span class="logout"><a href="logout.php">Logout</a></span>
    </div>
  </div>
</header>

<div class="container">
    <p>Selamat datang, <b><?= ucfirst($username); ?></b> 👋</p>

    <div class="box-container">
        <div class="box income">
            <h4>Pemasukan</h4>
            <p>Rp <?= number_format($total_pemasukan,0,',','.'); ?></p>
        </div>
        <div class="box outcome">
            <h4>Pengeluaran</h4>
            <p>Rp <?= number_format($total_pengeluaran,0,',','.'); ?></p>
        </div>
        <div class="box saldo">
            <h4>Saldo</h4>
            <p>Rp <?= number_format($saldo,0,',','.'); ?></p>
        </div>
    </div>

    <div class="action-btn">
        <a href="pemasukan.php?bulan=<?= $bulan; ?>" class="btn income">➕ Tambah Pemasukan</a>
        <a href="pengeluaran.php?bulan=<?= $bulan; ?>" class="btn outcome">➖ Tambah Pengeluaran</a>
        <a href="arsip.php" class="btn archive">📂 Lihat Arsip Semua Transaksi</a>
    </div>

    <form method="GET">
        <label>Pilih Bulan:</label>
        <select name="bulan" onchange="this.form.submit()">
            <?php
            $nama_bulan = ["01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"];
            foreach($nama_bulan as $m => $nama) {
                $s = ($bulan == $m) ? "selected" : "";
                echo "<option value='$m' $s>$nama</option>";
            }
            ?>
        </select>
    </form>

    <table class="table">
        <thead>
           <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Aksi</th>
           </tr> 
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                $kelas = ($row['jenis'] == 'pemasukan') ? 'pemasukan' : 'pengeluaran';
            ?>
            <tr class="<?= $kelas; ?>">
                <td><?= $no++; ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                <td><?= ucfirst($row['jenis']); ?></td>
                <td>Rp <?= number_format($row['jumlah'],0,',','.'); ?></td>
                <td><?= $row['keterangan']; ?></td>
                <td>
                    <a href="edit_transaksi.php?id=<?= $row['id']; ?>&bulan=<?= $bulan; ?>">Edit</a> |
                    <a href="hapus_transaksi.php?id=<?= $row['id']; ?>&bulan=<?= $bulan; ?>" onclick="return confirm('Hapus?');">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="chart-container">
        <h3 style="text-align:center; margin-bottom:10px;">Visualisasi Keuangan</h3>
        <canvas id="myChart"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pemasukan', 'Pengeluaran', 'Saldo'],
            datasets: [{
                label: 'Rupiah',
                data: [<?= $total_pemasukan; ?>, <?= $total_pengeluaran; ?>, <?= $saldo; ?>],
                backgroundColor: ['#27ae60', '#c0392b', '#2980b9']
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

</body>
</html>