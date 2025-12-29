<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$bulan_asal = $_GET['bulan'] ?? date('m'); 
$error = "";

if (isset($_POST['simpan'])) {
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal']; 

    if ($jumlah <= 0) {
        $error = "Jumlah pemasukan harus lebih dari 0!";
    } else {
        $query = mysqli_query($koneksi, "
            INSERT INTO transaksi (user_id, jenis, jumlah, keterangan, tanggal)
            VALUES ('$user_id', 'pemasukan', '$jumlah', '$keterangan', '$tanggal')
        ");

        if ($query) {
            $bulan_tujuan = date('m', strtotime($tanggal));
            $tahun_tujuan = date('Y', strtotime($tanggal));
            
            header("Location: dashboard.php?bulan=$bulan_tujuan&tahun=$tahun_tujuan");
            exit;
        } else {
            $error = "Gagal menyimpan data!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pemasukan</title>
    <style>
       body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f5f7f5;
        }

        .header {
            background: #63c400;
            height: 220px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header h1 {
            font-family: 'poppins', sans-serif;
            font-size:32px;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            margin-top: -80px;
            border-radius: 22px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.12);
        }

        label {
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
        }

        input {
            width: 100%;
            padding: 14px;
            border-radius: 50px;
            border: none;
            background: #f1f1f1;
            margin-bottom: 20px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 30px;
            background: #60ad18ff;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .back-link {
            margin-top: 15px;
            display: inline-block;
            font-size: 14px;
        }

    </style>
</head>
<body>

<div class="header">
    <h1>Tambah Pemasukan</h1>
</div>

<div class="container">
    <?php if (!empty($error)) { ?>
        <div class="eror"><?= $error; ?></div>
    <?php } ?>

    <div class="card">
        <form method="POST">
            <div class="inputBox">
                <label>Jumlah (Rp)</label>
                <input type="number" name="jumlah" placeholder="Masukkan jumlah pemasukan..." required>
            </div>

            <div class="inputBox">
                <label>Keterangan</label>
                <input type="text" name="keterangan" placeholder="Contoh: Gaji, uang jajan" required>
            </div>

            <div class="inputBox">
                <label>Tanggal Pemasukan</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
            </div>

            <input type="hidden" name="jenis" value="pemasukan">
            
            <button type="submit" name="simpan">Simpan </button>
        </form>

        <div class="back-link">
            <a href="dashboard.php?bulan=<?= $bulan_asal; ?>">⬅ Kembali ke Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>