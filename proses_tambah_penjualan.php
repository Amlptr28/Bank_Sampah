<?php
include 'config.php';

// Fungsi untuk membuat ID Penjualan baru dalam format P01, P02, dst.
function generateNewIdPenjualan($conn) {
    $result = $conn->query("SELECT MAX(ID_Penjualan) AS max_id FROM penjualan");
    $row = $result->fetch_assoc();
    $lastId = isset($row['max_id']) ? $row['max_id'] : 'P00';
    $newIdPenjualan = 'P' . str_pad((int)substr($lastId, 1) + 1, 2, '0', STR_PAD_LEFT);
    return $newIdPenjualan;
}

if (isset($_POST['tanggal'], $_POST['pembeli'], $_POST['id_sampah'], $_POST['berat_sampah'], $_POST['total_harga'])) {
    // Generate ID Penjualan baru
    $id_penjualan = generateNewIdPenjualan($conn);
    $tanggal = $_POST['tanggal'];
    $pembeli = $_POST['pembeli'];
    $id_sampah = $_POST['id_sampah'];
    $berat_sampah = $_POST['berat_sampah'];
    $total_harga = $_POST['total_harga'];

    // Hitung stok tersedia untuk jenis sampah yang dipilih
    $stokQuery = $conn->query("
        SELECT COALESCE(SUM(st.Berat_Sampah), 0) - COALESCE(SUM(p.Berat_Sampah), 0) AS Stock
        FROM sampah sm
        LEFT JOIN setoran st ON sm.ID_Sampah = st.ID_Sampah
        LEFT JOIN penjualan p ON sm.ID_Sampah = p.ID_Sampah
        WHERE sm.ID_Sampah = '$id_sampah'
        GROUP BY sm.ID_Sampah
    ");

    $stokRow = $stokQuery->fetch_assoc();
    $stokTersedia = isset($stokRow['Stock']) ? $stokRow['Stock'] : 0;

    // Cek apakah stok cukup untuk penjualan
    if ($berat_sampah > $stokTersedia) {
        echo json_encode(['status' => 'error', 'message' => 'Stok tidak cukup untuk melakukan penjualan']);
        exit;
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Insert data penjualan ke dalam tabel `penjualan`
        $stmt = $conn->prepare("INSERT INTO penjualan (ID_Penjualan, Tanggal, Pembeli, ID_Sampah, Berat_Sampah, Total_Harga) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdi", $id_penjualan, $tanggal, $pembeli, $id_sampah, $berat_sampah, $total_harga);

        if ($stmt->execute()) {
            // Commit transaksi jika berhasil
            $conn->commit();
            echo json_encode(['status' => 'success', 'redirect_url' => 'penjualan_Sampah.php']);
        } else {
            // Rollback jika terjadi kegagalan
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database']);
        }

        // Menutup statement
        $stmt->close();
    } catch (Exception $e) {
        // Rollback jika ada exception
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // Menutup koneksi
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data form tidak lengkap']);
}
?>
