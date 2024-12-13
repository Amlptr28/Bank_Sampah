<?php
// Include database configuration
include 'config.php';

// Include the FPDF library
require('fpdf/fpdf.php');

// Check if the ID_Nasabah is passed in the URL
if (isset($_GET['id'])) {
    $id_nasabah = $_GET['id'];
    $dateFilter = isset($_GET['date']) ? $_GET['date'] : null;

    // Modify the query to strictly filter by date if a date is selected
    $historyQuery = "
        SELECT 
            s.ID_Setoran as no,
            s.Tanggal as tanggal,
            sm.Jenis_Sampah as jenis_sampah,
            s.Berat_Sampah as berat_sampah,
            s.Total_Harga as total_harga
        FROM 
            setoran AS s
        JOIN 
            sampah AS sm ON s.ID_Sampah = sm.ID_Sampah
        WHERE 
            s.ID_Nasabah = ?";

    // Strictly filter by date if provided
    if ($dateFilter) {
        $historyQuery .= " AND DATE(s.Tanggal) = ?";
    }

    // Prepare and execute the query
    $historyStmt = $conn->prepare($historyQuery);
    if ($dateFilter) {
        $historyStmt->bind_param("ss", $id_nasabah, $dateFilter);
    } else {
        $historyStmt->bind_param("i", $id_nasabah);
    }
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();

    // Query to get the details of the selected nasabah
    $nasabahQuery = "SELECT * FROM nasabah WHERE ID_Nasabah = ?";
    $stmt = $conn->prepare($nasabahQuery);
    $stmt->bind_param("i", $id_nasabah);
    $stmt->execute();
    $nasabahResult = $stmt->get_result();
    $nasabah = $nasabahResult->fetch_assoc();

    // Create a new FPDF instance
    $pdf = new FPDF();
    $pdf->AddPage();

    // Set the font and size
    $pdf->SetFont('Arial', 'B', 16);

    // Add the title to the PDF
    $title = 'Riwayat Setoran Nasabah: ' . $nasabah['Nama_Nasabah'];
    if ($dateFilter) {
        $title .= ' (Tanggal: ' . $dateFilter . ')';
    }
    $pdf->Cell(0, 10, $title, 0, 1, 'C');

    // Add a line break
    $pdf->Ln(10);

    // Tambahkan informasi nasabah
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 7, 'Nama: ' . $nasabah['Nama_Nasabah'], 0, 1);
    $pdf->Cell(0, 7, 'Nomor Induk: ' . $nasabah['Nomor_Induk'], 0, 1);
    $pdf->Cell(0, 7, 'Alamat: ' . $nasabah['Alamat_Nasabah'], 0, 1);
    $pdf->Ln(10);

    // Cek apakah ada data yang difilter
    if ($historyResult->num_rows > 0) {
        // Set the font and size for the table headers
        $pdf->SetFont('Arial', 'B', 12);

        // Table headers
        $headers = array('No', 'Tanggal', 'Jenis Sampah', 'Berat Sampah (Kg)', 'Total Harga (Rp)');

        // Column widths
        $column_widths = array(10, 30, 50, 40, 50);

        // Header
        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($column_widths[$i], 7, $headers[$i], 1);
        }
        $pdf->Ln();

        // Set the font and size for the table data
        $pdf->SetFont('Arial', '', 12);

        // Variabel untuk total
        $total_berat = 0;
        $total_harga = 0;

        // Data
        $row_height = 6;
        $no = 1;
        while ($row = $historyResult->fetch_assoc()) {
            $pdf->Cell($column_widths[0], $row_height, $no, 1);
            $pdf->Cell($column_widths[1], $row_height, $row['tanggal'], 1);
            $pdf->Cell($column_widths[2], $row_height, $row['jenis_sampah'], 1);
            $pdf->Cell($column_widths[3], $row_height, $row['berat_sampah'], 1);
            $pdf->Cell($column_widths[4], $row_height, 'Rp ' . number_format($row['total_harga'], 0, ',', '.'), 1);
            $pdf->Ln();
            
            // Tambahkan ke total
            $total_berat += $row['berat_sampah'];
            $total_harga += $row['total_harga'];
            $no++;
        }

        // Tambahkan baris total
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($column_widths[0] + $column_widths[1] + $column_widths[2], $row_height, 'Total', 1);
        $pdf->Cell($column_widths[3], $row_height, number_format($total_berat, 2), 1);
        $pdf->Cell($column_widths[4], $row_height, 'Rp ' . number_format($total_harga, 0, ',', '.'), 1);
    } else {
        // Jika tidak ada data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Tidak ada riwayat setoran untuk periode ini.', 0, 1, 'C');
    }

    // Output the PDF to the browser
    $pdf->Output('riwayat_setoran_nasabah.pdf', 'I');
    exit;
} else {
    // If ID is not provided, redirect to the data nasabah page
    header("Location: data_nasabah.php");
    exit;
}
?>