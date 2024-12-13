<?php
// Masukkan konfigurasi database
include 'config.php';

// Query untuk mendapatkan data nasabah
$query = "SELECT * FROM nasabah ORDER BY ID_Nasabah ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nasabah - Bank Sampah Unit Sukses</title>
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

    <!-- Data Nasabah Title -->
    <div class="data-nasabah-title">Data Nasabah</div>

    <!-- Search and Table Container -->
    <div class="search-table-container">
        <!-- Search Bar with Search and Tambah Buttons -->
        <div class="search-bar">
            <input type="text" placeholder="Search by Name or Nomor Induk" id="searchInput">
            <button onclick="performSearch()">Search</button>
            <button class="tambah-button" onclick="window.location.href='Tambah_nasabah.php'">Tambah</button>
        </div>

        <!-- Data Table -->
        <table>
            <thead>
                <tr>
                    <th>ID Nasabah</th>
                    <th>Nama</th>
                    <th>Nomor Induk</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTable">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['ID_Nasabah'] ?>">
                            <td><?= $no ?></td>
                            <td><?= $row['Nama_Nasabah'] ?></td>
                            <td><?= $row['Nomor_Induk'] ?></td>
                            <td><?= $row['Alamat_Nasabah'] ?></td>
                            <td>
                                <button class="btn btn-edit" onclick="window.location.href='Lihat_nasabah.php?id=<?= $row['ID_Nasabah'] ?>'">Lihat</button>
                                <button class="btn btn-delete" onclick="deleteRecord('<?= $row['ID_Nasabah'] ?>')">Hapus</button>
                            </td>
                        </tr>
                        <?php $no++; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Tidak ada data nasabah</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Function to delete a record directly without confirmation
    function deleteRecord(id) {
        fetch(`hapus_Nasabah.php?id=${id}`, {
            method: "GET"
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Data berhasil dihapus.");
                window.location.reload(); // Reload halaman setelah penghapusan berhasil
            } else {
                alert("Gagal menghapus data: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menghapus data.");
        });
    }

    // Function to perform search based on Nama or Nomor Induk
    function performSearch() {
        const query = document.getElementById('searchInput').value.trim().toLowerCase();
        const rows = document.querySelectorAll('#dataTable tr');

        rows.forEach(row => {
            const nama = row.cells[1].textContent.toLowerCase();
            const nomorInduk = row.cells[2].textContent.toLowerCase();
            if (nama.includes(query) || nomorInduk.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
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
