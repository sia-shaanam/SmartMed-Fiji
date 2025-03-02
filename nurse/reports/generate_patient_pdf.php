<?php
require('fpdf.php');

// Database connection
include('smf_db_conn.php');

// Query to fetch patient data
$sql = "SELECT patient_id, firstName, lastName, preferredName, dob, email, phone, gender, maritalStatus 
        FROM patient";
$result = $conn->query($sql);

// Create a PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Add Title
$pdf->Cell(0, 10, 'Patient List', 0, 1, 'C');
$pdf->Ln(10); // Line break

// Table Header
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(50, 10, 'Patient ID', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'First Name', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Last Name', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Gender', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Phone', 1, 1, 'C', 1); // Last column needs a new line

// Table Body
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, $row['patient_id'], 1);
    $pdf->Cell(40, 10, $row['firstName'], 1);
    $pdf->Cell(40, 10, $row['lastName'], 1);
    $pdf->Cell(20, 10, $row['gender'], 1);
    $pdf->Cell(40, 10, $row['phone'], 1, 1); // Line break after each row
}

// Output PDF to browser for download
$pdf->Output('D', 'patient_list.pdf'); // Forces download

$conn->close();
?>
