<?php
// Memulai sesi
session_start();

// Memastikan pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Memasukkan konfigurasi database
include 'config.php';

// Memeriksa apakah data telah dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $id_sampah = $_POST['id_sampah'];
    $jenis_sampah = $_POST['jenis_sampah'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];

    // Query untuk memperbarui data sampah
    $query = "UPDATE sampah SET Jenis_Sampah = ?, Harga_Beli = ?, Harga_Jual = ? WHERE ID_Sampah = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $jenis_sampah, $harga_beli, $harga_jual, $id_sampah);

    // Menjalankan query dan memeriksa keberhasilan
    if ($stmt->execute()) {
        // Jika berhasil, arahkan kembali ke halaman data_sampah.php dengan pesan sukses
        header("Location: data_sampah.php?message=success");
        exit;
    } else {
        // Jika gagal, arahkan kembali dengan pesan error
        header("Location: edit_sampah.php?id=$id_sampah&error=update_failed");
        exit;
    }
    $stmt->close();
}
$conn->close();
?>
