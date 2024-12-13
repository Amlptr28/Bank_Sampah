<?php
// Include the database configuration
include 'config.php';

// Check if all necessary POST data is received
if (
    isset($_POST['id_setoran'], $_POST['tanggal'], $_POST['id_nasabah'], 
    $_POST['id_sampah'], $_POST['berat_sampah'], $_POST['total_harga'])
) {
    $id_setoran = $_POST['id_setoran'];
    $tanggal = $_POST['tanggal'];
    $id_nasabah = $_POST['id_nasabah'];
    $id_sampah = $_POST['id_sampah'];
    $berat_sampah = $_POST['berat_sampah'];
    
    // Calculate total price based on weight and price per kg of selected waste type
    $stmt = $conn->prepare("SELECT Harga_Beli FROM sampah WHERE ID_Sampah = ?");
    $stmt->bind_param("s", $id_sampah);
    $stmt->execute();
    $stmt->bind_result($harga_per_kg);
    $stmt->fetch();
    $stmt->close();

    // Calculate the total price
    $total_harga = $berat_sampah * $harga_per_kg;

    // Insert data into 'setoran' table
    $query = "INSERT INTO setoran (ID_Setoran, Tanggal, ID_Nasabah, ID_Sampah, Berat_Sampah, Total_Harga) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssid", $id_setoran, $tanggal, $id_nasabah, $id_sampah, $berat_sampah, $total_harga);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save data to the database']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete form data']);
}

// Close the database connection
$conn->close();
?>
