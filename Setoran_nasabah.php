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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setoran Nasabah - Bank Sampah Unit Sukses</title>
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

    <!-- Setoran Nasabah Title -->
    <div class="setoran-title">Setoran Nasabah</div>

    <!-- Search and Table Container -->
    <div class="search-table-container">
        <!-- Search Bar with Search and Tambah Buttons -->
        <div class="search-bar">
            <input type="text" placeholder="Search" id="searchInput">
            <button onclick="performSearch()">Search</button>
            <button class="tambah-button" onclick="window.location.href='tambah_setoran_nasabah.php'">Tambah</button>
        </div>
        <!-- Data Table -->
        <table>
            <thead>
                <tr>
                    <th>ID Setoran</th>
                    <th>Tanggal</th>
                    <th>Nama Nasabah</th>
                    <th>Jenis Sampah</th>
                    <th>Berat Sampah (Kg)</th>
                    <th>Total Harga (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTable">
                <?php
                // Query to join tables and get required fields
                $query = "
                    SELECT s.ID_Setoran, s.Tanggal, n.Nama_Nasabah, sm.Jenis_Sampah, s.Berat_Sampah, s.Total_Harga
                    FROM setoran s
                    JOIN nasabah n ON s.ID_Nasabah = n.ID_Nasabah
                    JOIN sampah sm ON s.ID_Sampah = sm.ID_Sampah
                    ORDER BY s.Tanggal DESC
                ";
                
                $result = $conn->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Format the Total Harga as "Rp xxx" without decimals
                        $formatted_harga = 'Rp ' . number_format($row['Total_Harga'], 0, ',', '.');
                        
                        echo "<tr data-id='{$row['ID_Setoran']}'>
                                <td>{$row['ID_Setoran']}</td>
                                <td>{$row['Tanggal']}</td>
                                <td>{$row['Nama_Nasabah']}</td>
                                <td>{$row['Jenis_Sampah']}</td>
                                <td>{$row['Berat_Sampah']}</td>
                                <td>$formatted_harga</td>
                                <td><button class='btn btn-delete' onclick=\"deleteRecord('{$row['ID_Setoran']}')\">Hapus</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data setoran</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
    // Function to perform search
    document.getElementById('searchInput').addEventListener('input', performSearch);

    function performSearch() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let rows = document.querySelectorAll('#dataTable tr');
        
        rows.forEach(row => {
            let cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.innerText.toLowerCase().includes(input)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }

    // Function to delete the record directly without confirmation
    function deleteRecord(id) {
        // Send GET request to delete the record
        fetch(`hapus_Setoran.php?id=${id}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Data berhasil dihapus.');
                location.reload(); // Reload the page after deletion
            } else {
                alert('Gagal menghapus data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data.');
        });
    }
    </script>
</body>
</html>
