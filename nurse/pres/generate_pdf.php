<?php
require('fpdf.php');

// Include database connection file
include('smf_db_conn.php');

// Retrieve prescription details
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch prescription details
    $sql = "SELECT p.patient_id, pat.firstName, pat.lastName, d.name AS doctor_name, p.prescription, p.created_at 
            FROM prescriptions p
            JOIN patient pat ON p.patient_id = pat.patient_id
            JOIN doctors d ON p.doctor_id = d.doctor_id
            WHERE p.id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($patient_id, $firstName, $lastName, $doctor_name, $prescription, $created_at);
    $stmt->fetch();
    $stmt->close();

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Header Section
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->Cell(0, 10, 'SmartMed Fiji Prescription', 0, 1, 'C');
    $pdf->Ln(10);

    // Table Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Patient ID:', 1, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient_id, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Patient Name:', 1, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $firstName . ' ' . $lastName, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Prescribed by:', 1, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $doctor_name, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Date:', 1, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, date('F j, Y', strtotime($created_at)), 1, 1);
    $pdf->Ln(10); // Add some space before the prescription

    // Prescription Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Prescription', 0, 1);
    $pdf->Ln(2);

    // Prescription Text
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, 'Instructions: ' . $prescription);
    $pdf->Ln(10);

    // Random doctor signature (Image)
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Doctor\'s Signature:', 0, 1, 'L');

    // Path to the signature image (replace with actual path)
    $signatureImages = ['1.jpg', 'OIP.jpg', 'download.jpg']; // Array of signature images
    $randomSignature = $signatureImages[array_rand($signatureImages)]; // Randomly select a signature
    $pdf->Image($randomSignature, 10, $pdf->GetY(), 50); // Adjust the position and size of the signature image

    // Footer Section
    $pdf->SetY(-50); // Position at 1.5 cm from bottom
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Please follow the instructions carefully.', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Thank you for choosing SmartMed Fiji!', 0, 1, 'C');

    // Output the PDF
    $pdf->Output();
} else {
    echo "No prescription ID provided.";
}

$conn->close();
?>
