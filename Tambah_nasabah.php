<?php
// Include your database configuration
include 'config.php';

// Fetch the latest ID Nasabah and add 1 to create a new ID
$result = $conn->query("SELECT MAX(ID_Nasabah) AS max_id FROM nasabah");
$row = $result->fetch_assoc();
$newIdNasabah = isset($row['max_id']) ? $row['max_id'] + 1 : 1;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id_nasabah = $_POST['id_nasabah'];
    $nama_nasabah = $_POST['nama_nasabah'];
    $nomor_induk = $_POST['nomor_induk'];
    $alamat_nasabah = $_POST['alamat_nasabah'];

    // Insert the new nasabah into the database
    $stmt = $conn->prepare("INSERT INTO nasabah (ID_Nasabah, Nama_Nasabah, Nomor_Induk, Alamat_Nasabah) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_nasabah, $nama_nasabah, $nomor_induk, $alamat_nasabah);
    
    if ($stmt->execute()) {
        // Redirect to the nasabah data page with success message
        echo json_encode(['status' => 'success', 'message' => 'Data nasabah berhasil ditambahkan!']);
    } else {
        // Return error message if the query fails
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menambahkan data.']);
    }

    // Close the prepared statement
    $stmt->close();
    // Close the database connection
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Nasabah - Bank Sampah Unit Sukses</title>
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
        <h2>TAMBAH NASABAH</h2>
        <form id="nasabahForm" method="POST">
            <input type="text" name="id_nasabah" placeholder="ID Nasabah" value="<?php echo $newIdNasabah; ?>" readonly>
            <input type="text" name="nama_nasabah" placeholder="Nama Nasabah" required>
            <input type="text" name="nomor_induk" placeholder="Nomor Induk" required>
            <input type="text" name="alamat_nasabah" placeholder="Alamat" required>
            <button type="submit" class="submit-button">TAMBAHKAN</button>
        </form>
    </div>

    <script>
    document.getElementById("nasabahForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("tambah_nasabah.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert(data.message);
                window.location.href = "data_nasabah.php";  // Redirect to data_nasabah page
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
