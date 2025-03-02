<?php
// Database connection
include('smf_db_conn.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $conn->real_escape_string($_POST['patient_id']);
    
    // Query to retrieve patient details
    $patient_query = "SELECT * FROM patient WHERE patient_id='$patient_id'";
    $patient_result = $conn->query($patient_query);
    
    // Check if patient exists
    if ($patient_result->num_rows > 0) {
        $patient_data = $patient_result->fetch_assoc();
        
        // Query to retrieve prescriptions
        $prescriptions_query = "SELECT * FROM prescriptions WHERE patient_id='$patient_id'";
        $prescriptions_result = $conn->query($prescriptions_query);
        
        // Query to retrieve telemedicine appointments
        $telemedicine_query = "SELECT * FROM telemedicine_appointments WHERE patient_id='$patient_id'";
        $telemedicine_result = $conn->query($telemedicine_query);
        
        // Query to retrieve regular appointments
        $appointments_query = "SELECT * FROM appointments WHERE patient_id='$patient_id'";
        $appointments_result = $conn->query($appointments_query);
        
        // Query to retrieve medical records
        $medical_records_query = "SELECT * FROM medical_records WHERE patient_id='$patient_id'";
        $medical_records_result = $conn->query($medical_records_query);

        // Include FPDF library
        require('fpdf.php');
        
        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Title
        $pdf->Cell(0, 10, 'Patient Records', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Patient Details Table
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Patient Details:', 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        
        // Header
        $pdf->Cell(50, 10, 'Field', 1);
        $pdf->Cell(140, 10, 'Value', 1);
        $pdf->Ln();

        // Patient Data Rows
        $pdf->SetFont('Arial', '', 10);
        foreach ($patient_data as $key => $value) {
            $pdf->Cell(50, 10, ucfirst($key), 1);
            $pdf->Cell(140, 10, $value, 1);
            $pdf->Ln();
        }
        $pdf->Ln(5);
        
        // Prescriptions Table
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Prescriptions:', 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        
        // Header
        $pdf->Cell(30, 10, 'Prescription ID', 1);
        $pdf->Cell(100, 10, 'Prescription', 1);
        $pdf->Cell(30, 10, 'Date Issued', 1);
        $pdf->Ln();

        // Prescriptions Data Rows
        $pdf->SetFont('Arial', '', 10);
        while ($prescription = $prescriptions_result->fetch_assoc()) {
            $pdf->Cell(30, 10, $prescription['prescription_id'], 1);
            $pdf->Cell(100, 10, $prescription['prescription'], 1);
            $pdf->Cell(30, 10, $prescription['date_issued'], 1);
            $pdf->Ln();
        }
        $pdf->Ln(5);
        
     
        // Medical Records Section
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Medical Records:', 0, 1);
        $pdf->SetFont('Arial', '', 10);

        // Medical Records Data
        while ($record = $medical_records_result->fetch_assoc()) {
            $pdf->MultiCell(0, 10, "Record ID: " . $record['record_id']);
            $pdf->MultiCell(0, 10, "Visit Date: " . $record['visit_date']);
            $pdf->MultiCell(0, 10, "Diagnosis: " . $record['diagnosis']);
            $pdf->MultiCell(0, 10, "Treatment Plans: " . $record['treatment_plans']);
            $pdf->MultiCell(0, 10, "Test Results: " . $record['test_results']);
            $pdf->MultiCell(0, 10, "Blood Pressure: " . $record['blood_pressure']);
            $pdf->MultiCell(0, 10, "Heart Rate: " . $record['heart_rate']);
            $pdf->MultiCell(0, 10, "Temperature: " . $record['temperature']);
            $pdf->MultiCell(0, 10, "Weight: " . $record['weight']);
            $pdf->MultiCell(0, 10, "Height: " . $record['height']);
            $pdf->MultiCell(0, 10, "Primary Physician: " . $record['primary_physician']);
            $pdf->MultiCell(0, 10, "Primary Physician Address: " . $record['primary_physician_address']);
            $pdf->MultiCell(0, 10, "Primary Physician Contact: " . $record['primary_physician_contact']);
            $pdf->MultiCell(0, 10, "Secondary Physician: " . $record['secondary_physician']);
            $pdf->MultiCell(0, 10, "Secondary Physician Contact: " . $record['secondary_physician_contact']);
            $pdf->MultiCell(0, 10, "Medical Conditions: " . $record['medical_conditions']);
            $pdf->MultiCell(0, 10, "Medications: " . $record['medications']);
            $pdf->MultiCell(0, 10, "Visit Reason: " . $record['visit_reason']);
            $pdf->MultiCell(0, 10, "Pregnancy Status: " . $record['pregnancy_status']);
            $pdf->MultiCell(0, 10, "Pregnancy Duration: " . $record['pregnancy_duration']);
            $pdf->MultiCell(0, 10, "Allergies: " . $record['allergies']);
            $pdf->MultiCell(0, 10, "Previous Injuries: " . $record['previous_injuries']);
            $pdf->MultiCell(0, 10, "Immunization History: " . $record['immunization_history']);
            $pdf->MultiCell(0, 10, "Family Medical History: " . $record['family_medical_history']);
            $pdf->MultiCell(0, 10, "Insurance Carrier: " . $record['insurance_carrier']);
            $pdf->MultiCell(0, 10, "Insurance Plan: " . $record['insurance_plan']);
            $pdf->MultiCell(0, 10, "Insurance Contact: " . $record['insurance_contact']);
            $pdf->MultiCell(0, 10, "Policy Number: " . $record['policy_number']);
            $pdf->MultiCell(0, 10, "Group Number: " . $record['group_number']);
            $pdf->MultiCell(0, 10, "SSN: " . $record['ssn']);
            $pdf->MultiCell(0, 10, "Employment Status: " . $record['employment_status']);
            $pdf->MultiCell(0, 10, "Occupation: " . $record['occupation']);
            $pdf->MultiCell(0, 10, "Industry: " . $record['industry']);
            $pdf->MultiCell(0, 10, "Company Name: " . $record['company_name']);
            $pdf->MultiCell(0, 10, "Company Address: " . $record['company_address']);
            $pdf->MultiCell(0, 10, "Company City: " . $record['company_city']);
            $pdf->MultiCell(0, 10, "Company State: " . $record['company_state']);
            $pdf->MultiCell(0, 10, "Company Zip: " . $record['company_zip']);
            $pdf->MultiCell(0, 10, "Present Symptoms: " . $record['present_symptoms']);
            $pdf->MultiCell(0, 10, "Symptom Details: " . $record['symptom_details']);
            $pdf->MultiCell(0, 10, "Symptom Duration: " . $record['symptom_duration']);
            $pdf->MultiCell(0, 10, "Symptom Severity: " . $record['symptom_severity']);
            $pdf->MultiCell(0, 10, "Exercise Frequency: " . $record['exercise_frequency']);
            $pdf->MultiCell(0, 10, "Diet: " . $record['diet']);
            $pdf->MultiCell(0, 10, "Sleep Patterns: " . $record['sleep_patterns']);
            $pdf->MultiCell(0, 10, "Stress Management: " . $record['stress_management']);
            $pdf->MultiCell(0, 10, "Substance Use: " . $record['substance_use']);
            $pdf->Ln(10);
        }
        $pdf->Ln(5);


   // Telemedicine Appointments Table
   $pdf->SetFont('Arial', 'B', 12);
   $pdf->Cell(0, 10, 'Telemedicine Appointments:', 0, 1);
   $pdf->SetFont('Arial', 'B', 10);
   
   // Header
   $pdf->Cell(30, 10, 'Appointment ID', 1);
   $pdf->Cell(50, 10, 'Doctor Name', 1);
   $pdf->Cell(30, 10, 'Date', 1);
   $pdf->Cell(30, 10, 'Time', 1);
   $pdf->Cell(30, 10, 'Status', 1);
   $pdf->Ln();

   // Telemedicine Data Rows
   $pdf->SetFont('Arial', '', 10);
   while ($appointment = $telemedicine_result->fetch_assoc()) {
       $pdf->Cell(30, 10, $appointment['appointment_id'], 1);
       $pdf->Cell(50, 10, $appointment['doctor_name'], 1);
       $pdf->Cell(30, 10, $appointment['appointment_date'], 1);
       $pdf->Cell(30, 10, $appointment['appointment_time'], 1);
       $pdf->Cell(30, 10, $appointment['status'], 1);
       $pdf->Ln();
   }
   $pdf->Ln(5);
   
   // Regular Appointments Table
   $pdf->SetFont('Arial', 'B', 12);
   $pdf->Cell(0, 10, 'Regular Appointments:', 0, 1);
   $pdf->SetFont('Arial', 'B', 10);
   
   // Header
   $pdf->Cell(30, 10, 'Appointment ID', 1);
   $pdf->Cell(50, 10, 'Doctor ID', 1);
   $pdf->Cell(30, 10, 'Date', 1);
   $pdf->Cell(30, 10, 'Time', 1);
   $pdf->Cell(30, 10, 'Status', 1);
   $pdf->Ln();

   // Regular Appointments Data Rows
   $pdf->SetFont('Arial', '', 10);
   while ($appointment = $appointments_result->fetch_assoc()) {
       $pdf->Cell(30, 10, $appointment['appointment_id'], 1);
       $pdf->Cell(50, 10, $appointment['doctor_id'], 1);
       $pdf->Cell(30, 10, $appointment['appointment_date'], 1);
       $pdf->Cell(30, 10, $appointment['appointment_time'], 1);
       $pdf->Cell(30, 10, $appointment['status'], 1);
       $pdf->Ln();
   }
   $pdf->Ln(5);


        // Output the PDF
        $pdf->Output('D', 'Patient_Records.pdf');
    } else {
        echo "Patient not found.";
    }
}

$conn->close();
?>
