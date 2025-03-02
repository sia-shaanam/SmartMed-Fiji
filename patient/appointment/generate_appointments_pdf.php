<?php
// Include FPDF library
require('fpdf.php');

// Start the session
session_start();

// Check if the patient is logged in
if (!isset($_SESSION['patient_id'])) {
    die("You must be logged in to view your appointments.");
}

// Database connection (Adjust with your DB credentials)
include('smf_db_conn.php');

// Get the logged-in patient's ID
$patient_id = $_SESSION['patient_id'];

// Query to retrieve appointments for the logged-in patient, ordered from the last to the most recent
$query = "
    SELECT * 
    FROM appointments 
    WHERE patient_id = ? 
    ORDER BY appointment_date DESC, appointment_time DESC
";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Create PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set font for title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'SmartMed Fiji', 0, 1, 'C');
$pdf->Cell(0, 10, 'Your Appointment History', 0, 1, 'C');
$pdf->Ln(10);

// Set font for headers
$pdf->SetFont('Arial', 'B', 12);

// Loop through each appointment
while ($appointment = $result->fetch_assoc()) {
    // Create a new table for each appointment
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Appointment with ' . $appointment['patient_name'], 0, 1);
    $pdf->Ln(5); // Space between title and table

    // Header for the appointment table
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, 'Date', 1);
    $pdf->Cell(40, 10, 'Time', 1);
    $pdf->Cell(40, 10, 'Status', 1);
    $pdf->Cell(70, 10, 'Reason', 1);
    $pdf->Ln();

    // Appointment details
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 10, $appointment['appointment_date'], 1);
    $pdf->Cell(40, 10, $appointment['appointment_time'], 1);
    $pdf->Cell(40, 10, $appointment['status'], 1);
    $pdf->Cell(70, 10, !empty($appointment['reason']) ? $appointment['reason'] : 'N/A', 1);
    $pdf->Ln();

    // If there are remarks, add them as a separate row
    if (!empty($appointment['remark'])) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, "Remark: " . $appointment['remark'], 0, 1);
    }

    $pdf->Ln(10); // Space before the next appointment
}

// Output the PDF
$pdf->Output('D', 'Appointment_Timeline.pdf'); // 'D': Download the file
?>
