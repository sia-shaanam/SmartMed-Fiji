<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 20px;
        }
        form {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #e6f7ff;
            border-radius: 10px;
        }
        input, textarea, select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Enter Medical Record</h2>
    <form action="insert_record.php" method="POST">
        <label>Patient ID:</label>
        <input type="text" name="patient_id" required>

        <label>Visit Date:</label>
        <input type="date" name="visit_date" required>

        <label>Diagnosis:</label>
        <textarea name="diagnosis"></textarea>

        <label>Treatment Plans:</label>
        <textarea name="treatment_plans"></textarea>

        <label>Test Results:</label>
        <textarea name="test_results"></textarea>

        <label>Blood Pressure:</label>
        <input type="text" name="blood_pressure">

        <label>Heart Rate:</label>
        <input type="text" name="heart_rate">

        <label>Temperature (°C):</label>
        <input type="number" step="0.01" name="temperature">

        <label>Weight (kg):</label>
        <input type="number" step="0.01" name="weight">

        <label>Height (cm):</label>
        <input type="number" step="0.01" name="height">

        <label>Primary Physician:</label>
        <input type="text" name="primary_physician">

        <label>Primary Physician Address:</label>
        <input type="text" name="primary_physician_address">

        <label>Primary Physician Contact:</label>
        <input type="text" name="primary_physician_contact">

        <label>Secondary Physician:</label>
        <input type="text" name="secondary_physician">

        <label>Secondary Physician Contact:</label>
        <input type="text" name="secondary_physician_contact">

        <label>Medical Conditions:</label>
        <textarea name="medical_conditions"></textarea>

        <label>Medications:</label>
        <textarea name="medications"></textarea>

        <label>Visit Reason:</label>
        <textarea name="visit_reason"></textarea>

        <label>Pregnancy Status:</label>
        <select name="pregnancy_status">
            <option value="N/A">N/A</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>

        <label>Pregnancy Duration (if applicable):</label>
        <input type="text" name="pregnancy_duration">

        <label>Allergies:</label>
        <textarea name="allergies"></textarea>

        <label>Previous Injuries:</label>
        <textarea name="previous_injuries"></textarea>

        <label>Immunization History:</label>
        <textarea name="immunization_history"></textarea>

        <label>Family Medical History:</label>
        <textarea name="family_medical_history"></textarea>

        <label>Insurance Carrier:</label>
        <input type="text" name="insurance_carrier">

        <label>Insurance Plan:</label>
        <input type="text" name="insurance_plan">

        <label>Insurance Contact:</label>
        <input type="text" name="insurance_contact">

        <label>Policy Number:</label>
        <input type="text" name="policy_number">

        <label>Group Number:</label>
        <input type="text" name="group_number">

        <label>SSN:</label>
        <input type="text" name="ssn">

        <label>Employment Status:</label>
        <input type="text" name="employment_status">

        <label>Occupation:</label>
        <input type="text" name="occupation">

        <label>Industry:</label>
        <input type="text" name="industry">

        <label>Company Name:</label>
        <input type="text" name="company_name">

        <label>Company Address:</label>
        <input type="text" name="company_address">

        <label>Company City:</label>
        <input type="text" name="company_city">

        <label>Company State:</label>
        <input type="text" name="company_state">

        <label>Company ZIP:</label>
        <input type="text" name="company_zip">

        <label>Present Symptoms:</label>
        <textarea name="present_symptoms"></textarea>

        <label>Symptom Details:</label>
        <textarea name="symptom_details"></textarea>

        <label>Symptom Duration:</label>
        <input type="text" name="symptom_duration">

        <label>Symptom Severity:</label>
        <input type="text" name="symptom_severity">

        <label>Exercise Frequency:</label>
        <input type="text" name="exercise_frequency">

        <label>Diet:</label>
        <input type="text" name="diet">

        <label>Sleep Patterns:</label>
        <input type="text" name="sleep_patterns">

        <label>Stress Management:</label>
        <input type="text" name="stress_management">

        <label>Substance Use:</label>
        <input type="text" name="substance_use">

        <button type="submit">Submit Record</button>
    </form>
</body>
</html>
