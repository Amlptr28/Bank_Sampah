<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Setoran - Bank Sampah Unit Sukses</title>
    <style>
        /* General Styles */
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
        }

        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-info img {
            width: 24px;
            vertical-align: middle;
        }

        /* Form Container */
        .form-container {
            background-color: #F8E9C8;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            width: 320px;
            margin: 50px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container input[type="text"], .form-container input[type="date"], .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        .submit-button {
            width: 100%;
            padding: 10px;
            background-color: #689B70;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        .submit-button:hover {
            background-color: #567c57;
        }
    </style>
</head>
<body>
    <?php
        include 'config.php';

        function generateNextId($conn) {
            $result = $conn->query("SELECT ID_Setoran FROM setoran ORDER BY ID_Setoran DESC LIMIT 1");

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $lastId = $row['ID_Setoran'];
                $number = (int)substr($lastId, 1);
                $number++;
                $newId = 'S' . str_pad($number, 2, '0', STR_PAD_LEFT);
            } else {
                $newId = "S01";
            }

            return $newId;
        }

        $newId = generateNextId($conn);

        // Fetch registered nasabah
        $nasabahOptions = '';
        $nasabahResult = $conn->query("SELECT ID_Nasabah, Nama_Nasabah FROM nasabah");
        while ($nasabah = $nasabahResult->fetch_assoc()) {
            $nasabahOptions .= "<option value='{$nasabah['ID_Nasabah']}'>{$nasabah['Nama_Nasabah']}</option>";
        }

        // Fetch registered sampah with prices
        $sampahOptions = '';
        $sampahResult = $conn->query("SELECT ID_Sampah, Jenis_Sampah, Harga_Beli FROM sampah");
        $sampahData = [];
        while ($sampah = $sampahResult->fetch_assoc()) {
            $sampahOptions .= "<option value='{$sampah['ID_Sampah']}' data-price='{$sampah['Harga_Beli']}'>{$sampah['Jenis_Sampah']}</option>";
            $sampahData[$sampah['ID_Sampah']] = $sampah['Harga_Beli'];
        }
    ?>

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
        <h2>TAMBAH SETORAN</h2>
        <form id="setoranForm">
            <input type="text" name="id_setoran" placeholder="ID Setoran" value="<?php echo $newId; ?>" readonly>
            <input type="date" name="tanggal" required>
            <select name="id_nasabah" required>
                <option value="" disabled selected>Pilih Nama Nasabah</option>
                <?php echo $nasabahOptions; ?>
            </select>
            <select name="id_sampah" id="jenisSampah" required>
                <option value="" disabled selected>Pilih Jenis Sampah</option>
                <?php echo $sampahOptions; ?>
            </select>
            <input type="text" name="berat_sampah" id="beratSampah" placeholder="Berat Sampah (Kg)" required>
            <input type="text" name="total_harga" id="totalHarga" placeholder="Total Harga (Rp)" readonly>
            <button type="submit" class="submit-button">TAMBAHKAN</button>
        </form>
    </div>

    <script>
    // Handle the total price calculation based on selected sampah and weight
    document.getElementById("jenisSampah").addEventListener("change", calculateTotalPrice);
    document.getElementById("beratSampah").addEventListener("input", calculateTotalPrice);

    function calculateTotalPrice() {
        const jenisSampah = document.getElementById("jenisSampah");
        const beratSampah = parseFloat(document.getElementById("beratSampah").value) || 0;
        const selectedOption = jenisSampah.options[jenisSampah.selectedIndex];
        const hargaBeli = parseFloat(selectedOption.getAttribute("data-price")) || 0;

        const totalHarga = hargaBeli * beratSampah;
        document.getElementById("totalHarga").value = totalHarga ? `Rp ${totalHarga.toLocaleString("id-ID")}` : "";
    }

    // Form submission
    document.getElementById("setoranForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("proses_tambah_setoran.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Setoran berhasil ditambahkan!");
                window.location.href = "setoran_nasabah.php";
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
