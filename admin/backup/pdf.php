<?php
// Suppress error display to avoid output issues before PDF generation
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Disable error display

require('fpdf.php');
include('smf_db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get doctor ID from the form submission
    $doctor_id = $_POST['doctor_id'];

    // Create a new PDF instance
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Doctor Records', 0, 1, 'C');
    $pdf->Ln(10);

    // Fetch doctor details
    $doctor_query = "SELECT * FROM doctors WHERE doctor_id = ?";
    $stmt = $conn->prepare($doctor_query);
    $stmt->bind_param("s", $doctor_id);
    $stmt->execute();
    $doctor_result = $stmt->get_result();

    // Doctor Information Table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Doctor Information', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 10, 'Doctor ID', 1);
    $pdf->Cell(30, 10, 'Name', 1);
    $pdf->Cell(40, 10, 'Specialization', 1);
    $pdf->Cell(40, 10, 'Contact Info', 1);
    $pdf->Cell(40, 10, 'Email', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    if ($doctor_result->num_rows > 0) {
        $doctor = $doctor_result->fetch_assoc();
        $pdf->Cell(20, 10, $doctor['doctor_id'], 1);
        $pdf->Cell(30, 10, $doctor['name'], 1);
        $pdf->Cell(40, 10, $doctor['specialization'], 1);
        $pdf->Cell(40, 10, $doctor['contact_information'], 1);
        $pdf->Cell(40, 10, $doctor['email'], 1);
        $pdf->Ln();
    } else {
        $pdf->Cell(0, 10, 'No doctor found with this ID.', 0, 1);
        $pdf->Ln(10);
    }

    // Fetch associated appointments
    $appointment_query = "SELECT * FROM appointments WHERE doctor_id = ?";
    $stmt = $conn->prepare($appointment_query);
    $stmt->bind_param("s", $doctor_id);
    $stmt->execute();
    $appointments = $stmt->get_result();

    // Appointments Table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Appointments', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, 'Appointment ID', 1);
    $pdf->Cell(50, 10, 'Patient Name', 1);
    $pdf->Cell(30, 10, 'Date', 1);
    $pdf->Cell(30, 10, 'Time', 1);
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    while ($appointment = $appointments->fetch_assoc()) {
        $pdf->Cell(40, 10, $appointment['appointment_id'], 1);
        $pdf->Cell(50, 10, $appointment['patient_name'], 1);
        $pdf->Cell(30, 10, $appointment['appointment_date'], 1);
        $pdf->Cell(30, 10, $appointment['appointment_time'], 1);
        $pdf->Cell(30, 10, $appointment['status'], 1);
        $pdf->Ln();
    }
    $pdf->Ln(10);

    // Fetch associated prescriptions
    $prescription_query = "SELECT * FROM prescriptions WHERE doctor_id = ?";
    $stmt = $conn->prepare($prescription_query);
    $stmt->bind_param("s", $doctor_id);
    $stmt->execute();
    $prescriptions = $stmt->get_result();

    // Prescriptions Table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Prescriptions', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, 'Prescription ID', 1);
    $pdf->Cell(50, 10, 'Patient ID', 1);
    $pdf->Cell(60, 10, 'Prescription', 1);
    $pdf->Cell(40, 10, 'Date Issued', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    while ($prescription = $prescriptions->fetch_assoc()) {
        $pdf->Cell(40, 10, $prescription['prescription_id'], 1);
        $pdf->Cell(50, 10, (isset($prescription['patient_id']) ? $prescription['patient_id'] : 'N/A'), 1);
        $pdf->Cell(60, 10, $prescription['prescription'], 1);
        $pdf->Cell(40, 10, (isset($prescription['date_issued']) ? $prescription['date_issued'] : 'N/A'), 1);
        $pdf->Ln();
    }
    $pdf->Ln(10);

    // Fetch associated telemedicine appointments
    $telemedicine_query = "SELECT * FROM telemedicine_appointments WHERE doctor_id = ?";
    $stmt = $conn->prepare($telemedicine_query);
    $stmt->bind_param("s", $doctor_id);
    $stmt->execute();
    $telemedicine_appointments = $stmt->get_result();

    // Telemedicine Appointments Table
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Telemedicine Appointments', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, 'Telemedicine ID', 1);
    $pdf->Cell(50, 10, 'Patient Name', 1);
    $pdf->Cell(30, 10, 'Date', 1);
    $pdf->Cell(30, 10, 'Time', 1);
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    while ($telemedicine = $telemedicine_appointments->fetch_assoc()) {
        $pdf->Cell(40, 10, $telemedicine['telemedicine_id'], 1);
        $pdf->Cell(50, 10, 
            (isset($telemedicine['patient_first_name']) ? $telemedicine['patient_first_name'] : 'N/A') . ' ' . 
            (isset($telemedicine['patient_last_name']) ? $telemedicine['patient_last_name'] : 'N/A'), 1);
        $pdf->Cell(30, 10, $telemedicine['appointment_date'], 1);
        $pdf->Cell(30, 10, $telemedicine['appointment_time'], 1);
        $pdf->Cell(30, 10, $telemedicine['status'], 1);
        $pdf->Ln();
    }

    // Output the PDF
    $pdf->Output('D', 'Doctor_Records.pdf');
}

// Close the database connection
$conn->close();
?>
