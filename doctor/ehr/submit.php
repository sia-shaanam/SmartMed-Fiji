<?php
// Database connection
include('smf_db_conn.php');

// Sanitize input data to prevent SQL injection
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Patient data from POST request
$patient_id = sanitize_input($_POST['patient_id']);
$login_id = sanitize_input($_POST['login_id']);
$name = sanitize_input($_POST['name']);
$specialization = sanitize_input($_POST['specialization']);
$contact_information = sanitize_input($_POST['contact_information']);
$email = sanitize_input($_POST['email']);
$department = sanitize_input($_POST['department']);
$job_position = sanitize_input($_POST['job_position']);
$gender = sanitize_input($_POST['gender']);
$age = sanitize_input($_POST['age']);
$firstName = sanitize_input($_POST['firstName']);
$lastName = sanitize_input($_POST['lastName']);
$preferredName = sanitize_input($_POST['preferredName']);
$dob = sanitize_input($_POST['dob']);
$patientIdentifier = sanitize_input($_POST['patientIdentifier']);
$preferredPronouns = sanitize_input($_POST['preferredPronouns']);
$maritalStatus = sanitize_input($_POST['maritalStatus']);
$address = sanitize_input($_POST['address']);
$phone = sanitize_input($_POST['phone']);
$contactPreference = sanitize_input($_POST['contactPreference']);
$emergencyContactName = sanitize_input($_POST['emergencyContactName']);
$relationship = sanitize_input($_POST['relationship']);
$emergencyContactNumber = sanitize_input($_POST['emergencyContactNumber']);

// Medical Records data from POST request
$record_id = sanitize_input($_POST['record_id']);
$visit_date = sanitize_input($_POST['visit_date']);
$diagnosis = sanitize_input($_POST['diagnosis']);
$treatment_plans = sanitize_input($_POST['treatment_plans']);
$test_results = sanitize_input($_POST['test_results']);
$blood_pressure = sanitize_input($_POST['blood_pressure']);
$heart_rate = sanitize_input($_POST['heart_rate']);
$temperature = sanitize_input($_POST['temperature']);
$weight = sanitize_input($_POST['weight']);
$height = sanitize_input($_POST['height']);
$primary_physician = sanitize_input($_POST['primary_physician']);
$primary_physician_address = sanitize_input($_POST['primary_physician_address']);
$primary_physician_contact = sanitize_input($_POST['primary_physician_contact']);
$secondary_physician = sanitize_input($_POST['secondary_physician']);
$secondary_physician_contact = sanitize_input($_POST['secondary_physician_contact']);
$medical_conditions = sanitize_input($_POST['medical_conditions']);
$medications = sanitize_input($_POST['medications']);
$visit_reason = sanitize_input($_POST['visit_reason']);
$pregnancy_status = sanitize_input($_POST['pregnancy_status']);
$pregnancy_duration = sanitize_input($_POST['pregnancy_duration']);
$allergies = sanitize_input($_POST['allergies']);
$previous_injuries = sanitize_input($_POST['previous_injuries']);
$immunization_history = sanitize_input($_POST['immunization_history']);
$family_medical_history = sanitize_input($_POST['family_medical_history']);
$insurance_carrier = sanitize_input($_POST['insurance_carrier']);
$insurance_plan = sanitize_input($_POST['insurance_plan']);
$insurance_contact = sanitize_input($_POST['insurance_contact']);
$policy_number = sanitize_input($_POST['policy_number']);
$group_number = sanitize_input($_POST['group_number']);
$ssn = sanitize_input($_POST['ssn']);
$employment_status = sanitize_input($_POST['employment_status']);
$occupation = sanitize_input($_POST['occupation']);
$industry = sanitize_input($_POST['industry']);
$company_name = sanitize_input($_POST['company_name']);
$company_address = sanitize_input($_POST['company_address']);
$company_city = sanitize_input($_POST['company_city']);
$company_state = sanitize_input($_POST['company_state']);
$company_zip = sanitize_input($_POST['company_zip']);
$present_symptoms = sanitize_input($_POST['present_symptoms']);
$symptom_details = sanitize_input($_POST['symptom_details']);
$symptom_duration = sanitize_input($_POST['symptom_duration']);
$symptom_severity = sanitize_input($_POST['symptom_severity']);
$exercise_frequency = sanitize_input($_POST['exercise_frequency']);
$diet = sanitize_input($_POST['diet']);
$sleep_patterns = sanitize_input($_POST['sleep_patterns']);
$stress_management = sanitize_input($_POST['stress_management']);
$substance_use = sanitize_input($_POST['substance_use']);

// Start transaction
$conn->begin_transaction();

try {
    // SQL query to update patient table
    $update_patient_sql = "UPDATE patient SET
        login_id='$login_id',
        contact_information='$contact_information',
        email='$email',
        job_position='$job_position',
        gender='$gender',
        age='$age',
        firstName='$firstName',
        lastName='$lastName',
        preferredName='$preferredName',
        dob='$dob',
        preferredPronouns='$preferredPronouns',
        maritalStatus='$maritalStatus',
        address='$address',
        phone='$phone',
        contactPreference='$contactPreference',
        emergencyContactName='$emergencyContactName',
        relationship='$relationship',
        emergencyContactNumber='$emergencyContactNumber'
        WHERE patient_id='$patient_id'";

    // Execute patient update query
    if (!$conn->query($update_patient_sql)) {
        throw new Exception("Error updating patient table: " . $conn->error);
    }

    // SQL query to update medical_records table
    $update_medical_records_sql = "UPDATE medical_records SET
        visit_date='$visit_date',
        diagnosis='$diagnosis',
        treatment_plans='$treatment_plans',
        test_results='$test_results',
        blood_pressure='$blood_pressure',
        heart_rate='$heart_rate',
        temperature='$temperature',
        weight='$weight',
        height='$height',
        primary_physician='$primary_physician',
        primary_physician_address='$primary_physician_address',
        primary_physician_contact='$primary_physician_contact',
        secondary_physician='$secondary_physician',
        secondary_physician_contact='$secondary_physician_contact',
        medical_conditions='$medical_conditions',
        medications='$medications',
        visit_reason='$visit_reason',
        pregnancy_status='$pregnancy_status',
        pregnancy_duration='$pregnancy_duration',
        allergies='$allergies',
        previous_injuries='$previous_injuries',
        immunization_history='$immunization_history',
        family_medical_history='$family_medical_history',
        insurance_carrier='$insurance_carrier',
        insurance_plan='$insurance_plan',
        insurance_contact='$insurance_contact',
        policy_number='$policy_number',
        group_number='$group_number',
        ssn='$ssn',
        employment_status='$employment_status',
        occupation='$occupation',
        industry='$industry',
        company_name='$company_name',
        company_address='$company_address',
        company_city='$company_city',
        company_state='$company_state',
        company_zip='$company_zip',
        present_symptoms='$present_symptoms',
        symptom_details='$symptom_details',
        symptom_duration='$symptom_duration',
        symptom_severity='$symptom_severity',
        exercise_frequency='$exercise_frequency',
        diet='$diet',
        sleep_patterns='$sleep_patterns',
        stress_management='$stress_management',
        substance_use='$substance_use'
        WHERE record_id='$record_id'";

    // Execute medical records update query
    if (!$conn->query($update_medical_records_sql)) {
        throw new Exception("Error updating medical records table: " . $conn->error);
    }

    // Commit the transaction
    $conn->commit();
    echo "<script>
        alert('Records updated successfully!');
        window.location.href = 'ehr_main.php';
    </script>";

} catch (Exception $e) {
    // Rollback the transaction if any query fails
    $conn->rollback();
    echo "<script>
        alert('Error updating records: " . addslashes($e->getMessage()) . "');
        window.location.href = 'update_pat.php';
    </script>";
}

// Close the connection
$conn->close();
?>
