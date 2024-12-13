<?php
// Include database configuration
include 'config.php';

// Check if ID_Sampah is in the URL to display data to edit
if (isset($_GET['id'])) {
    $id_sampah = $_GET['id'];

    // Retrieve data for the specified ID_Sampah
    $query = "SELECT * FROM sampah WHERE ID_Sampah = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_sampah);
    $stmt->execute();
    $result = $stmt->get_result();
    $sampah = $result->fetch_assoc();

    // Redirect to data_sampah if no data is found
    if (!$sampah) {
        header("Location: data_sampah.php");
        exit;
    }
} else {
    header("Location: data_sampah.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sampah - Bank Sampah Unit Sukses</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the external CSS -->
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
        <h2>EDIT SAMPAH</h2>
        <form action="proses_edit_sampah.php" method="POST">
            <input type="text" name="id_sampah" placeholder="ID Sampah" value="<?php echo $sampah['ID_Sampah']; ?>" readonly>
            <input type="text" name="jenis_sampah" placeholder="Jenis Sampah" value="<?php echo $sampah['Jenis_Sampah']; ?>" required>
            <input type="text" name="harga_beli" placeholder="Harga Beli" value="<?php echo number_format($sampah['Harga_Beli'], 0, '', ''); ?>" required>
            <input type="text" name="harga_jual" placeholder="Harga Jual" value="<?php echo number_format($sampah['Harga_Jual'], 0, '', ''); ?>" required>
            <button type="submit" class="submit-button">PERBARUI</button>
        </form>
    </div>
</body>
</html>
