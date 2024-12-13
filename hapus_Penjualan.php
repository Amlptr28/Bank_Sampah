<?php
// Include the database configuration
include 'config.php';

header('Content-Type: application/json'); // Set header to return JSON response

if (isset($_GET['id'])) {
    $id_penjualan = $_GET['id'];

    // Prepare the query to delete the record based on ID_Penjualan
    $query = "DELETE FROM penjualan WHERE ID_Penjualan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_penjualan); // "s" for string

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data dari database']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
}

$conn->close();
?>
