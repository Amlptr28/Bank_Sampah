<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_nasabah = $_POST['nama_nasabah'];
    $nomor_induk = $_POST['nomor_induk'];
    $alamat_nasabah = $_POST['alamat_nasabah'];

    // SQL query to insert the new nasabah
    $query = "INSERT INTO nasabah (Nama_Nasabah, Nomor_Induk, Alamat_Nasabah) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nama_nasabah, $nomor_induk, $alamat_nasabah);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambah nasabah']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid']);
}
?>
