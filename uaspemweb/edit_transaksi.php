<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan");
}
$bulan = $_GET['bulan'] ?? date('m');
$query = mysqli_query($koneksi, "
    SELECT * FROM transaksi 
    WHERE id='$id' AND user_id='$user_id'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan atau bukan milik Anda");
}

// Proses update
if (isset($_POST['update'])) {
    $jenis = $_POST['jenis'];
    $jumlah = (int) $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'];

    if ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0";
    } else {
        mysqli_query($koneksi, "
            UPDATE transaksi SET 
                jenis='$jenis',
                jumlah='$jumlah',
                keterangan='$keterangan',
                tanggal='$tanggal'
            WHERE id='$id' AND user_id='$user_id'
        ");

        header("Location: dashboard.php?bulan=$bulan");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
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
    <div class="container">
        <h1>Edit Transaksi</h1>
    </div>
</div>


<div class="form-box">

<?php if (!empty($error)) { ?>
    <p class="error"><?= $error; ?></p>
<?php } ?>

<div class="container">
    <div class="card">
        <form method="POST">
            <div class="inputBox">
                <label>Jenis Transaksi</label>
                <select name="jenis" required>
                <option value="pemasukan" <?= $data['jenis']=='pemasukan'?'selected':''; ?>>Pemasukan</option>
                <option value="pengeluaran" <?= $data['jenis']=='pengeluaran'?'selected':''; ?>>Pengeluaran</option>
                </select>
                <br><br>
            </div>

            <div class="inputBox">
                <label>Jumlah (Rp)</label>
                <input type="number" name="jumlah" value="<?= $data['jumlah']; ?>" required>
                <br><br>
            </div>

            <div class="inputBox">
                <label>Keterangan</label>
                <input type="text" name="keterangan" value="<?= $data['keterangan']; ?>" required>
                <br><br>
            </div>

            <div class="inputBox">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d', strtotime($data['tanggal'])); ?>" required>
                <br><br>
            </div>

            <button type="submit" name="update">Update</button>
        </form>
        <div class="back-link">
            <a href="dashboard.php?bulan=<?= $bulan; ?>">⬅ Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>
