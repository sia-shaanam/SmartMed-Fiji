<?php
// Include the FPDF library
require('fpdf.php');

// Assuming you already have a session management system in place
session_start();

// Retrieve the logged-in patient's ID from the session
$patient_id = $_SESSION['patient_id'];

// Database connection (replace with your actual DB credentials)
$host = 'localhost'; 
$dbname = 'smf_db'; 
$username = 'root'; 
$password = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Fetch medical records for the logged-in patient
$query = $conn->prepare('SELECT * FROM medical_records WHERE patient_id = :patient_id');
$query->bindParam(':patient_id', $patient_id);
$query->execute();
$record = $query->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    echo "No medical records found for the patient.";
    exit;
}

// Create a new PDF document
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add document title
$pdf->Cell(0, 10, 'SmartMed Fiji', 0, 1, 'C');
$pdf->Cell(0, 10, 'Patient Medical Records', 0, 1, 'C');
$pdf->Ln(10);

// Add patient general information
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Patient ID: ' . $record['patient_id'], 0, 1);
$pdf->Cell(0, 10, 'Visit Date: ' . $record['visit_date'], 0, 1);
$pdf->Ln(5);

// Add medical information
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Medical Information', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Diagnosis: ' . $record['diagnosis'], 0, 1);
$pdf->Cell(0, 10, 'Treatment Plans: ' . $record['treatment_plans'], 0, 1);
$pdf->Cell(0, 10, 'Test Results: ' . $record['test_results'], 0, 1);
$pdf->Ln(5);

// Add vital signs
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Vital Signs & Physical Measurements', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Blood Pressure: ' . $record['blood_pressure'], 0, 1);
$pdf->Cell(0, 10, 'Heart Rate: ' . $record['heart_rate'], 0, 1);
$pdf->Cell(0, 10, 'Temperature: ' . $record['temperature'] . ' °C', 0, 1);
$pdf->Cell(0, 10, 'Weight: ' . $record['weight'] . ' kg', 0, 1);
$pdf->Cell(0, 10, 'Height: ' . $record['height'] . ' cm', 0, 1);
$pdf->Ln(5);

// Add insurance details
$pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Visit Date: ' . $record['visit_date'], 0, 1);
        $pdf->Cell(0, 10, 'Diagnosis: ' . $record['diagnosis'], 0, 1);
        $pdf->Cell(0, 10, 'Treatment Plans: ' . $record['treatment_plans'], 0, 1);
        $pdf->Cell(0, 10, 'Test Results: ' . $record['test_results'], 0, 1);
        $pdf->Cell(0, 10, 'Blood Pressure: ' . $record['blood_pressure'], 0, 1);
        $pdf->Cell(0, 10, 'Heart Rate: ' . $record['heart_rate'], 0, 1);
        $pdf->Cell(0, 10, 'Temperature: ' . $record['temperature'], 0, 1);
        $pdf->Cell(0, 10, 'Weight: ' . $record['weight'], 0, 1);
        $pdf->Cell(0, 10, 'Height: ' . $record['height'], 0, 1);
        $pdf->Cell(0, 10, 'Primary Physician: ' . $record['primary_physician'], 0, 1);
        $pdf->Cell(0, 10, 'Primary Physician Address: ' . $record['primary_physician_address'], 0, 1);
        $pdf->Cell(0, 10, 'Primary Physician Contact: ' . $record['primary_physician_contact'], 0, 1);
        $pdf->Cell(0, 10, 'Secondary Physician: ' . $record['secondary_physician'], 0, 1);
        $pdf->Cell(0, 10, 'Secondary Physician Contact: ' . $record['secondary_physician_contact'], 0, 1);
        $pdf->Cell(0, 10, 'Medical Conditions: ' . $record['medical_conditions'], 0, 1);
        $pdf->Cell(0, 10, 'Medications: ' . $record['medications'], 0, 1);
        $pdf->Cell(0, 10, 'Visit Reason: ' . $record['visit_reason'], 0, 1);
        $pdf->Cell(0, 10, 'Pregnancy Status: ' . $record['pregnancy_status'], 0, 1);
        $pdf->Cell(0, 10, 'Pregnancy Duration: ' . $record['pregnancy_duration'], 0, 1);
        $pdf->Cell(0, 10, 'Allergies: ' . $record['allergies'], 0, 1);
        $pdf->Cell(0, 10, 'Previous Injuries: ' . $record['previous_injuries'], 0, 1);
        $pdf->Cell(0, 10, 'Immunization History: ' . $record['immunization_history'], 0, 1);
        $pdf->Cell(0, 10, 'Family Medical History: ' . $record['family_medical_history'], 0, 1);
        $pdf->Cell(0, 10, 'Insurance Carrier: ' . $record['insurance_carrier'], 0, 1);
        $pdf->Cell(0, 10, 'Insurance Plan: ' . $record['insurance_plan'], 0, 1);
        $pdf->Cell(0, 10, 'Insurance Contact: ' . $record['insurance_contact'], 0, 1);
        $pdf->Cell(0, 10, 'Policy Number: ' . $record['policy_number'], 0, 1);
        $pdf->Cell(0, 10, 'Group Number: ' . $record['group_number'], 0, 1);
        $pdf->Cell(0, 10, 'SSN: ' . $record['ssn'], 0, 1);
        $pdf->Cell(0, 10, 'Employment Status: ' . $record['employment_status'], 0, 1);
        $pdf->Cell(0, 10, 'Occupation: ' . $record['occupation'], 0, 1);
        $pdf->Cell(0, 10, 'Industry: ' . $record['industry'], 0, 1);
        $pdf->Cell(0, 10, 'Company Name: ' . $record['company_name'], 0, 1);
        $pdf->Cell(0, 10, 'Company Address: ' . $record['company_address'], 0, 1);
        $pdf->Cell(0, 10, 'Company City: ' . $record['company_city'], 0, 1);
        $pdf->Cell(0, 10, 'Company State: ' . $record['company_state'], 0, 1);
        $pdf->Cell(0, 10, 'Company Zip: ' . $record['company_zip'], 0, 1);
        $pdf->Cell(0, 10, 'Present Symptoms: ' . $record['present_symptoms'], 0, 1);
        $pdf->Cell(0, 10, 'Symptom Details: ' . $record['symptom_details'], 0, 1);
        $pdf->Cell(0, 10, 'Symptom Duration: ' . $record['symptom_duration'], 0, 1);
        $pdf->Cell(0, 10, 'Symptom Severity: ' . $record['symptom_severity'], 0, 1);
        $pdf->Cell(0, 10, 'Exercise Frequency: ' . $record['exercise_frequency'], 0, 1);
        $pdf->Cell(0, 10, 'Diet: ' . $record['diet'], 0, 1);
        $pdf->Cell(0, 10, 'Sleep Patterns: ' . $record['sleep_patterns'], 0, 1);
        $pdf->Cell(0, 10, 'Stress Management: ' . $record['stress_management'], 0, 1);
        $pdf->Cell(0, 10, 'Substance Use: ' . $record['substance_use'], 0, 1);

$pdf->Ln(10);

// Output the PDF
$pdf->Output('D', 'Medical_Records.pdf');
?>
