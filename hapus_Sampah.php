<?php
// Koneksi ke database
include 'config.php';

// Periksa apakah parameter 'id' ada di URL
if (isset($_GET['id'])) {
    $id_sampah = $_GET['id'];

    // Prepare query untuk menghapus data
    $query = "DELETE FROM sampah WHERE ID_Sampah = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_sampah);

    // Jalankan query
    if ($stmt->execute()) {
        // Jika berhasil, kirim respons JSON dengan status 'success'
        echo json_encode(['status' => 'success']);
    } else {
        // Jika gagal, kirim respons JSON dengan status 'error'
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close();
} else {
    // Jika tidak ada 'id' yang diberikan, kirim respons error
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
}
?>