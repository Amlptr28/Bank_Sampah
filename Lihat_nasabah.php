<?php

// Include database configuration
include 'config.php';

// Check if the ID_Nasabah is passed in the URL
if (isset($_GET['id'])) {
    $id_nasabah = $_GET['id'];
    $dateFilter = isset($_GET['date']) ? $_GET['date'] : ''; // Capture filter date from GET parameter

    // Modify the query to filter by date if a date is selected
    $historyQuery = "
        SELECT 
            s.ID_Setoran as no,
            s.Tanggal as tanggal,
            sm.Jenis_Sampah as jenis_sampah,
            s.Berat_Sampah as berat_sampah,
            s.Total_Harga as total_harga
        FROM 
            setoran AS s
        JOIN 
            sampah AS sm ON s.ID_Sampah = sm.ID_Sampah
        WHERE 
            s.ID_Nasabah = ?";

    // If a date filter is applied, modify the query to include a WHERE condition for the selected date
    if ($dateFilter) {
        $historyQuery .= " AND s.Tanggal = ?";
    }

    // Prepare and execute the query
    $historyStmt = $conn->prepare($historyQuery);
    if ($dateFilter) {
        $historyStmt->bind_param("ss", $id_nasabah, $dateFilter);  // bind both ID and date
    } else {
        $historyStmt->bind_param("i", $id_nasabah);  // bind only ID
    }
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();

    // Query to get the details of the selected nasabah
    $nasabahQuery = "SELECT * FROM nasabah WHERE ID_Nasabah = ?";
    $stmt = $conn->prepare($nasabahQuery);
    $stmt->bind_param("i", $id_nasabah);
    $stmt->execute();
    $nasabahResult = $stmt->get_result();
    $nasabah = $nasabahResult->fetch_assoc();

    // If no nasabah found, redirect to the data nasabah page
    if (!$nasabah) {
        header("Location: data_nasabah.php");
        exit;
    }

    // Check if the download button is clicked
    if (isset($_POST['download'])) {
        // Redirect to the cetak_pdf.php page with the necessary parameters
        header("Location: cetak_pdf.php?id=" . $id_nasabah . "&date=" . $dateFilter);
        exit;
    }

} else {
    // If ID is not provided, redirect to the data nasabah page
    header("Location: data_nasabah.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Setoran Nasabah - Bank Sampah Unit Sukses</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the external CSS -->
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

    <!-- Riwayat Setoran Title -->
    <div class="riwayat-setoran-title">Riwayat Setoran Nasabah: <?php echo $nasabah['Nama_Nasabah']; ?></div>

    <!-- Search and Table Container -->
    <div class="search-table-container">
        <!-- Search Bar with Filter and Unduh Buttons -->
        <div class="search-bar">
            <input type="date" placeholder="Filter Tanggal" id="dateFilter" style="width: 200px;">
            <button onclick="performSearch()">Filter</button>
            <div class="download-container">
                <form method="post">
                    <button type="submit" name="download">Unduh PDF</button>
                </form>
            </div>
        </div>

        <!-- Table Container -->
        <table id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis Sampah</th>
                    <th>Berat Sampah (Kg)</th>
                    <th>Total Harga (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($historyResult && $historyResult->num_rows > 0) {
                    $no = 1; // Initialize counter variable
                    // Display each row of data
                    while ($row = $historyResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['tanggal']}</td>
                                <td>{$row['jenis_sampah']}</td>
                                <td>{$row['berat_sampah']}</td>
                                <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                              </tr>";
                        $no++; // Increment counter after each row
                    }
                } else {
                    // If no data found
                    echo "<tr><td colspan='5'>Tidak ada riwayat setoran untuk nasabah ini</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript to handle date filtering -->
    <script>
    // Function to perform search/filter based on date
    function performSearch() {
        let filterDate = document.getElementById('dateFilter').value;
        let table = document.getElementById('dataTable');
        let rows = table.getElementsByTagName('tr');

        // Loop through all table rows, except the header
        for (let i = 1; i < rows.length; i++) {
            let dateCell = rows[i].cells[1];  // Second column (Tanggal)
            let rowDate = dateCell.innerText.trim();

            // Filter rows based on selected date
            if (filterDate === '' || rowDate === filterDate) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    // Optional: Reset filter when date input is cleared
    document.getElementById('dateFilter').addEventListener('input', function () {
        if (this.value === "") {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => row.style.display = '');
        }
    });
    </script>
</body>
</html>