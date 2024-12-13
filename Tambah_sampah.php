<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Sampah - Bank Sampah Unit Sukses</title>
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

        .form-container input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-container input[type="text"]:disabled {
            background-color: #e0e0e0;
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
            $result = $conn->query("SELECT ID_Sampah FROM sampah ORDER BY ID_Sampah DESC LIMIT 1");

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $lastId = $row['ID_Sampah'];

                $prefix = substr($lastId, 0, 1);
                $number = (int)substr($lastId, 1);

                $number++;

                if ($number >= 10) {
                    $prefix++;
                    $number = 1;
                }

                $newId = $prefix . str_pad($number, 2, '0', STR_PAD_LEFT);
            } else {
                $newId = "A01";
            }

            return $newId;
        }

        $newId = generateNextId($conn);
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
        <h2>TAMBAH SAMPAH</h2>
        <form id="sampahForm">
            <input type="text" name="id_sampah" placeholder="ID Sampah" value="<?php echo $newId; ?>" readonly>
            <input type="text" name="jenis_sampah" placeholder="Jenis Sampah" required>
            <input type="text" name="harga_beli" placeholder="Harga Beli" required>
            <input type="text" name="harga_jual" placeholder="Harga Jual" required>
            <button type="submit" class="submit-button">TAMBAHKAN</button>
        </form>
    </div>

    <script>
    document.getElementById("sampahForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("proses_tambah_sampah.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Data sampah berhasil ditambahkan!");
                window.location.href = "data_sampah.php";
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
