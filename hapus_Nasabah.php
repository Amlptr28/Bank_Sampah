<?php
// Konfigurasi database
include 'config.php';

if (isset($_GET['id'])) {
    $id_nasabah = $_GET['id'];

    // Query untuk menghapus data berdasarkan ID_Nasabah
    $query = "DELETE FROM nasabah WHERE ID_Nasabah = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("i", $id_nasabah); // Menggunakan "i" untuk integer sesuai tipe data ID_Nasabah

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
}
?>
