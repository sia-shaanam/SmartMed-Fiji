<?php
require('fpdf.php');

// Database connection
include('smf_db_conn.php');
// Query to fetch all doctors
$sql = "SELECT doctor_id, user_id, name, specialization, contact_information, email, department, job_position, gender, age FROM doctors";
$result = $conn->query($sql);

// Create PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Add Title
$pdf->Cell(0, 10, 'Smartmed Fiji', 0, 1, 'C');
$pdf->Ln(10); // Line break
$pdf->Cell(0, 10, 'Doctors List', 0, 1, 'C');
$pdf->Ln(10); // Line break

// Table Header
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(30, 10, 'Doctor ID', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Name', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Specialization', 1, 0, 'C', 1);
$pdf->Cell(60, 10, 'Email', 1, 1, 'C', 1); // Last cell needs line break

// Table Body
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['doctor_id'], 1);
    $pdf->Cell(30, 10, $row['name'], 1);
    $pdf->Cell(40, 10, $row['specialization'] ?? 'N/A', 1);
    $pdf->Cell(60, 10, $row['email'], 1, 1); // Line break after each row
}

// Output PDF to browser
$pdf->Output('D', 'doctors_list.pdf'); // Forces download

$conn->close();
?>
