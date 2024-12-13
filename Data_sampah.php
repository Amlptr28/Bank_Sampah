<?php
// Include the database connection
include 'config.php';

// Fetch data from the database
$result = $conn->query("SELECT * FROM sampah ORDER BY ID_Sampah ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sampah - Bank Sampah Unit Sukses</title>
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

    <!-- Data Sampah Title -->
    <div class="data-sampah-title">Data Sampah</div>

    <!-- Search and Table Container -->
    <div class="search-table-container">
        <!-- Search Bar with Search and Tambah Buttons -->
        <div class="search-bar">
            <input type="text" placeholder="Cari berdasarkan ID atau Jenis Sampah" id="searchInput">
            <button onclick="performSearch()">Search</button>
            <button class="tambah-button" onclick="window.location.href='tambah_sampah.php'">Tambah</button>
        </div>

        <!-- Data Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Sampah</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTable">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr data-id='{$row['ID_Sampah']}'>
                                <td>{$row['ID_Sampah']}</td>
                                <td>{$row['Jenis_Sampah']}</td>
                                <td>Rp " . number_format($row['Harga_Beli'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($row['Harga_Jual'], 0, ',', '.') . "</td>
                                <td>
                                    <button class='btn btn-edit' onclick=\"window.location.href='edit_sampah.php?id={$row['ID_Sampah']}'\">Edit</button>
                                    <button class='btn btn-delete' onclick=\"deleteRecord('{$row['ID_Sampah']}')\">Hapus</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data sampah</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
   
    function deleteRecord(id) {
        // Kirim permintaan GET ke hapus_sampah.php dengan ID data
        fetch(`hapus_sampah.php?id=${id}`, {
            method: "GET"
        })
        .then(response => response.json())  // Parse respons sebagai JSON
        .then(data => {
            if (data.status === "success") {
                alert("Data berhasil dihapus.");
                window.location.reload(); // Muat ulang halaman setelah penghapusan berhasil
            } else {
                alert("Gagal menghapus data: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menghapus data.");
        });
    }   

    // Function to perform search based on ID and Jenis Sampah
    function performSearch() {
        const query = document.getElementById('searchInput').value.trim().toLowerCase();
        const rows = document.querySelectorAll('#dataTable tr');

        rows.forEach(row => {
            const id = row.cells[0].textContent.toLowerCase();
            const jenis = row.cells[1].textContent.toLowerCase();

            if (id.includes(query) || jenis.includes(query)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Reset table when search input is cleared
    document.getElementById('searchInput').addEventListener('input', function () {
        if (this.value === "") {
            const rows = document.querySelectorAll('#dataTable tr');
            rows.forEach(row => row.style.display = '');
        }
    });
    </script>
</body>
</html>
