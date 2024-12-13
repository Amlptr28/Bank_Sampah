<?php
include 'config.php';

// Generate a new ID for Penjualan in format P01, P02, etc.
$result = $conn->query("SELECT MAX(ID_Penjualan) AS max_id FROM penjualan");
$row = $result->fetch_assoc();
$lastId = isset($row['max_id']) ? $row['max_id'] : 'P00';
$newIdPenjualan = 'P' . str_pad((int)substr($lastId, 1) + 1, 2, '0', STR_PAD_LEFT);

// Fetch available waste types that have stock
$sampahResult = $conn->query("SELECT sm.ID_Sampah, sm.Jenis_Sampah, sm.Harga_Jual, 
                                     COALESCE(SUM(st.Berat_Sampah), 0) - COALESCE(SUM(p.Berat_Sampah), 0) AS Stock
                              FROM sampah sm
                              LEFT JOIN setoran st ON sm.ID_Sampah = st.ID_Sampah
                              LEFT JOIN penjualan p ON sm.ID_Sampah = p.ID_Sampah
                              GROUP BY sm.ID_Sampah, sm.Jenis_Sampah, sm.Harga_Jual
                              HAVING Stock > 0"); // Only show items with stock > 0

// Create an array for JavaScript usage (for stock and price data)
$sampahData = [];
while ($sampah = $sampahResult->fetch_assoc()) {
    $sampahData[] = $sampah;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penjualan - Bank Sampah Unit Sukses</title>
    <link rel="stylesheet" href="style.css">
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
    <!-- Form Container -->
    <div class="form-container">
        <h2>TAMBAH PENJUALAN</h2>
        <form id="penjualanForm">
            <input type="text" name="id_penjualan" placeholder="No" value="<?php echo $newIdPenjualan; ?>" readonly>
            <input type="date" name="tanggal" placeholder="Tanggal" required>
            <input type="text" name="pembeli" placeholder="Pembeli" required>

            <!-- Jenis Sampah Dropdown -->
            <select name="id_sampah" id="jenisSampah" required>
                <option value="">Pilih Jenis Sampah</option>
                <?php foreach ($sampahData as $sampah): ?>
                    <option value="<?php echo $sampah['ID_Sampah']; ?>" data-harga="<?php echo $sampah['Harga_Jual']; ?>" data-stock="<?php echo $sampah['Stock']; ?>">
                        <?php echo $sampah['Jenis_Sampah']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="stock-display" id="stockDisplay">Stok tersedia: </div>

            <input type="number" step="0.01" name="berat_sampah" id="beratSampah" placeholder="Berat Sampah (Kg)" required>
            <input type="number" step="0.01" name="total_harga" id="totalHarga" placeholder="Total Harga (Rp)" readonly>
            <button type="submit" class="submit-button">TAMBAHKAN</button>
        </form>
    </div>

    <script>
    const jenisSampahDropdown = document.getElementById("jenisSampah");
    const beratSampahInput = document.getElementById("beratSampah");
    const totalHargaInput = document.getElementById("totalHarga");
    const stockDisplay = document.getElementById("stockDisplay");

    // Update stock display when a new waste type is selected
    jenisSampahDropdown.addEventListener("change", function() {
        const selectedOption = jenisSampahDropdown.options[jenisSampahDropdown.selectedIndex];
        const stock = parseFloat(selectedOption.getAttribute("data-stock") || 0);
        stockDisplay.textContent = "Stok tersedia: " + stock + " Kg";
        calculateTotalPrice();
    });

    // Calculate total price based on weight and unit price
    beratSampahInput.addEventListener("input", calculateTotalPrice);

    function calculateTotalPrice() {
        const selectedOption = jenisSampahDropdown.options[jenisSampahDropdown.selectedIndex];
        const unitPrice = parseFloat(selectedOption.getAttribute("data-harga") || 0);
        const stock = parseFloat(selectedOption.getAttribute("data-stock") || 0);
        const weight = parseFloat(beratSampahInput.value);

        // Validate weight based on available stock
        if (weight > stock) {
            alert("Berat sampah melebihi stok yang tersedia.");
            beratSampahInput.value = stock; // Limit to available stock
        }
        totalHargaInput.value = (weight * unitPrice).toFixed(2);
    }

    document.getElementById("penjualanForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("proses_tambah_penjualan.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Data penjualan berhasil ditambahkan!");
                window.location.href = data.redirect_url; // Redirect to penjualan_Sampah.php on success
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menambahkan data.");
        });
    });
    </script>
</body>
</html>
