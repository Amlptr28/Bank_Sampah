<?php
// Masukkan konfigurasi database
include 'config.php';

// Query untuk mendapatkan total nasabah
$query_nasabah = "SELECT COUNT(*) AS total_nasabah FROM nasabah";
$result_nasabah = mysqli_query($conn, $query_nasabah);
$data_nasabah = mysqli_fetch_assoc($result_nasabah);
$total_nasabah = $data_nasabah['total_nasabah'];

// Query untuk mendapatkan total penjualan sampah
$query_penjualan = "SELECT SUM(Total_Harga) AS total_penjualan FROM penjualan";
$result_penjualan = mysqli_query($conn, $query_penjualan);
$data_penjualan = mysqli_fetch_assoc($result_penjualan);
$total_penjualan = $data_penjualan['total_penjualan'] ?? 0; // Nilai default jika null

// Query untuk mendapatkan total setoran sampah
$query_setoran = "SELECT SUM(Berat_Sampah) AS total_setoran FROM setoran";
$result_setoran = mysqli_query($conn, $query_setoran);
$data_setoran = mysqli_fetch_assoc($result_setoran);
$total_setoran = $data_setoran['total_setoran'] ?? 0; // Nilai default jika null
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank Sampah Unit Sukses</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <img src="foto/logo.png" alt="Bank Sampah Unit Sukses Logo" class="logo">
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="data_sampah.php">Sampah</a>
            <a href="data_nasabah.php">Nasabah</a>
            <div class="dropdown">
                <span class="dropdown-toggle">Transaksi ▼</span>
                <div class="dropdown-content">
                    <a href="setoran_nasabah.php">Setoran Nasabah</a>
                    <a href="penjualan_sampah.php">Penjualan Sampah</a>
                </div>
            </div>
        </div>
        <div class="user-info">
            <img src="foto/logout.png" alt="User Icon">
            <a href="logout.php" style="text-decoration: none; color: #333; font-weight: bold;">
                <span>BSU Sukses</span>
            </a>
        </div>
    </div>

    <!-- Dashboard Title -->
    <div class="dashboard-title">Dashboard</div>

    <!-- Card Container -->
    <div class="card-container">
        <div class="card">
            Penjualan Sampah<br>Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?>
        </div>
        <div class="card card-pink">
            Nasabah<br><?php echo $total_nasabah; ?>
        </div>
        <div class="card card-purple">
            Setoran Sampah<br><?php echo number_format($total_setoran, 2, ',', '.'); ?> Kg
        </div>
    </div>
</body>
</html>