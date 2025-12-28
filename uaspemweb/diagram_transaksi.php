<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$query = "
    SELECT 
        YEAR(tanggal) AS bulan, 
        SUM(CASE WHEN jenis='pemasukan' THEN jumlah ELSE 0 END) AS total_pemasukan,
        SUM(CASE WHEN jenis='pengeluaran' THEN jumlah ELSE 0 END) AS total_pengeluaran
    FROM transaksi
    WHERE user_id='{$_SESSION['user_id']}'";

$hasil = mysqli_query($koneksi, $query);

$bulan = [];
$pemasukan = [];
$pengeluaran = []; 

while ($row = mysqli_fetch_assoc($hasil)) {
    $bulan[] = $row['bulan'];
    $pemasukan[] = $row['total_pemasukan'];
    $pengeluaran[] = $row['total_pengeluaran'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagram Keuangan Tahunan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h3>Diagram Pemasukan & Pengeluaran Tahunan</h3>

<canvas id="diagramTahunan"></canvas>

<?php

$tahun = [];
$pemasukan = [];
$pengeluaran = [];
?>

<script>
const tahun = <?= json_encode($tahun); ?>;
const pemasukan = <?= json_encode($pemasukan); ?>;
const pengeluaran = <?= json_encode($pengeluaran); ?>;


new Chart(document.getElementById('diagramTahunan'), {
    type: 'bar',
    data: {
        labels: tahun,
        datasets: [
            {
                label: 'Pemasukan (Rp)',
                data: pemasukan
            },
            {
                label: 'Pengeluaran (Rp)',
                data: pengeluaran
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>

