<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan input data dari form
    $id_sampah = $conn->real_escape_string(trim($_POST['id_sampah']));
    $jenis_sampah = $conn->real_escape_string(trim($_POST['jenis_sampah']));
    $harga_beli = $conn->real_escape_string(trim($_POST['harga_beli']));
    $harga_jual = $conn->real_escape_string(trim($_POST['harga_jual']));

    // Validasi harga_beli dan harga_jual untuk memastikan berupa angka
    if (!is_numeric($harga_beli) || !is_numeric($harga_jual)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Harga Beli dan Harga Jual harus berupa angka.'
        ]);
        exit;
    }

    // SQL untuk menyisipkan data baru ke tabel sampah
    $sql = "INSERT INTO sampah (ID_Sampah, Jenis_Sampah, Harga_Beli, Harga_Jual) 
            VALUES ('$id_sampah', '$jenis_sampah', '$harga_beli', '$harga_jual')";

    // Eksekusi query dan cek hasilnya
    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, kirim data yang baru ditambahkan sebagai respons JSON
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id_sampah' => $id_sampah,
                'jenis_sampah' => $jenis_sampah,
                'harga_beli' => number_format($harga_beli, 2, ',', '.'),
                'harga_jual' => number_format($harga_jual, 2, ',', '.')
            ]
        ]);
    } else {
        // Jika terjadi kesalahan, kirim pesan error
        echo json_encode([
            'status' => 'error', 
            'message' => 'Gagal menyimpan data: ' . $conn->error
        ]);
    }

    // Tutup koneksi database
    $conn->close();
} else {
    // Jika bukan metode POST, kirimkan respons error
    echo json_encode([
        'status' => 'error', 
        'message' => 'Permintaan tidak valid.'
    ]);
}
?>
