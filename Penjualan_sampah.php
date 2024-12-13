<?php
// Start session to check if the user is logged in
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Include database configuration
include 'config.php';

// Default bulan dan tahun untuk filter
$selectedMonth = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selectedYear = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query to calculate total saldo based on selected month and year
$totalSaldoQuery = $conn->prepare("
    SELECT COALESCE(SUM(Total_Harga), 0) AS total_saldo 
    FROM penjualan 
    WHERE MONTH(Tanggal) = ? AND YEAR(Tanggal) = ?
");
$totalSaldoQuery->bind_param('ii', $selectedMonth, $selectedYear);
$totalSaldoQuery->execute();
$totalSaldoRow = $totalSaldoQuery->get_result()->fetch_assoc();
$totalSaldo = $totalSaldoRow['total_saldo'];

// Query to fetch sales data based on selected month and year
$query = $conn->prepare("
    SELECT p.ID_Penjualan, p.Tanggal, p.Pembeli, sm.Jenis_Sampah, p.Berat_Sampah, p.Total_Harga
    FROM penjualan p
    JOIN sampah sm ON p.ID_Sampah = sm.ID_Sampah
    WHERE MONTH(p.Tanggal) = ? AND YEAR(p.Tanggal) = ?
    ORDER BY p.Tanggal DESC
");
$query->bind_param('ii', $selectedMonth, $selectedYear);
$query->execute();
$salesData = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Sampah - Bank Sampah Unit Sukses</title>
    <style>
        /* Basic Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        /* Header */
        .header {
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header img.logo {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 200px;
            height: auto;
        }

        .nav {
            margin-left: 120px;
            display: flex;
            gap: 60px;
            justify-content: center;
            flex-grow: 1;
            position: relative;
        }

        .nav a, .dropdown-toggle {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            cursor: pointer;
        }

        .nav a:hover, .dropdown-toggle:hover {
            color: #689B70;
        }

        /* Dropdown styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            margin-top: 5px;
            border-radius: 5px;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .dropdown-content a {
            color: #333;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-weight: normal;
        }
        
        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

        /* User Icon and Name */
        .user-info {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-info img {
            width: 24px;
            vertical-align: middle;
        }
        
        /* Page Title and Saldo */
        .penjualan-title {
            margin-top: 60px;
            padding: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }
        
        .saldo-total {
            text-align: center;
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Search and Table Container */
        .search-table-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            width: 100%;
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 80%;
            margin-bottom: 20px;
        }
        
        .search-bar .tambah-button {
            margin-left: auto;
            background-color: #f8e1a6;
            color: #333;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }

        .filter-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 20px;
            gap: 10px;
        }

        .filter-container select, .filter-container button {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .filter-container button {
            background-color: #689B70;
            color: #fff;
            cursor: pointer;
        }

        .filter-container button:hover {
            background-color: #567a58;
        }
        
        /* Table Styles */
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .btn {
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
        }
        
        .btn-delete {
            background-color: #f44336;
        }
        
        .btn-delete:hover {
            background-color: #e31b0c;
        }
    </style>
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

    <!-- Penjualan Sampah Title -->
    <div class="penjualan-title">Penjualan Sampah</div>


<!-- Saldo Total -->
<div class="saldo-total">
    Saldo Total: <span id="saldoTotal">Rp <?php echo number_format($totalSaldo, 0, ',', '.'); ?></span>
</div>


<!-- Search and Table Container -->
<div class="search-table-container">
<div class="search-bar" style="justify-content: space-between; width: 80%; margin: auto;">
    <form method="GET" action="penjualan_sampah.php" style="display: flex; align-items: center; gap: 10px;">
        <label for="bulan">Bulan:</label>
        <select name="bulan" id="bulan">
            <?php
            for ($i = 1; $i <= 12; $i++) {
                $selected = ($i == $selectedMonth) ? "selected" : "";
                echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
            }
            ?>
        </select>
        <label for="tahun">Tahun:</label>
        <select name="tahun" id="tahun">
            <?php
            $currentYear = date('Y');
            for ($i = $currentYear - 5; $i <= $currentYear; $i++) {
                $selected = ($i == $selectedYear) ? "selected" : "";
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>
    <a href="tambah_penjualan.php" class="tambah-button">Tambah</a>
</div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th>ID Penjualan</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Nama Sampah</th>
                <th>Berat Sampah (Kg)</th>
                <th>Total Harga (Rp)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="dataTable">
            <?php
            if ($salesData->num_rows > 0) {
                while ($row = $salesData->fetch_assoc()) {
                    $formatted_harga = 'Rp ' . number_format($row['Total_Harga'], 0, ',', '.');
                    echo "<tr>
                            <td>{$row['ID_Penjualan']}</td>
                            <td>{$row['Tanggal']}</td>
                            <td>{$row['Pembeli']}</td>
                            <td>{$row['Jenis_Sampah']}</td>
                            <td>{$row['Berat_Sampah']}</td>
                            <td>$formatted_harga</td>
                            <td><button class='btn btn-delete' onclick=\"deleteRecord('{$row['ID_Penjualan']}')\">Hapus</button></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Tidak ada data penjualan</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
function deleteRecord(id) {
    if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        fetch(`hapus_penjualan.php?id=${id}`, {
            method: "GET"
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Data berhasil dihapus.");
                window.location.reload();
            } else {
                alert("Gagal menghapus data: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menghapus data.");
        });
    }
}
function deleteRecord(id) {
    // Mengirimkan permintaan penghapusan data menggunakan fetch
    fetch(`hapus_penjualan.php?id=${id}`, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
            if (data.status === "success") {
                alert("Data berhasil dihapus.");
                window.location.reload();
            } else {
                alert("Gagal menghapus data: " + data.message);
            }
        })
    .catch(error => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menghapus data.");
    });
}

</script>
</body>
</html>