<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
include 'smf_db_conn.php';  // Assuming you have a connection script

// Fetch patient_id from the query parameter
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    // Fetch patient details
    $patient_sql = "SELECT * FROM patient WHERE patient_id = ?";
    $stmt = $conn->prepare($patient_sql);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $patient_result = $stmt->get_result();
    $patient = $patient_result->fetch_assoc();

    // Fetch medical records details
    $medical_sql = "SELECT * FROM medical_records WHERE patient_id = ?";
    $stmt = $conn->prepare($medical_sql);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $medical_result = $stmt->get_result();
    $medical_records = $medical_result->fetch_assoc();
} else {
    echo "No patient ID provided!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Patient and Medical Records</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your CSS file for styling -->
</head>
<style>
    /* General Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color:  #EDFBFE; /* Light teal background */
        color: #333;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        background-color: #ffffff; /* White background for the form */
        padding: 30px;
        border-radius: 10px;

    }

    /* Header Styles */
    header h2 {
        text-align: center;
        color:#04c9f5; /* Dark teal for header */
        font-size: 1.8rem;
        margin-bottom: 30px;
    }

    /* Form Section Titles */
    form h3 {
        color: #1abc9c; /* Light green for section titles */
        font-size: 1.4rem;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin: 25px 0 15px;
    }

    /* Flexbox for Form Rows */
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
        
    }

    /* Column Styles */
    .form-column {
        flex: 1;
        min-width: 300px;
        padding: 10px;
            border: 1px solid #f1f1f1;
            border-radius: 8px;
            background-color: #f9f9f9;
           
    }

    /* Label and Input Styles */
    label {
        margin-top: 10px;
        font-weight: bold;
        color: #34495e;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #cfd8dc;
        border-radius: 4px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #1abc9c; /* Green border on focus */
        box-shadow: 0 0 5px rgba(26, 188, 156, 0.3);
    }

    textarea {
        height: 120px; /* Increase default height for textareas */
    }

    /* Button Styles */
    button[type="submit"] {
        margin-top: 20px;
        padding: 12px;
        background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); /* Gradient button */
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
        background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%); /* Reverse gradient on hover */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .form-row {
            flex-direction: column;
        }

        button[type="submit"] {
            padding: 10px;
            font-size: 15px;
        }
    }

    /* Style for read-only fields */
    input[readonly],
    textarea[readonly] {
        background-color: #f1f1f1;
        border-color: #ddd;
        color: #999;
    }
    .section {
            border-left: 4px solid #4CAF50;
            padding-left: 20px;
            background-color: #f7fdf7; /* Light green background for sections */
            margin-bottom: 20px;
        }

        /* Medical Records Section Styles */
        .medical-records {
            border-left: 4px solid #3498db; /* Blue border for medical records section */
            background-color: #eef7fd; /* Light blue background for medical records */
        }

        /* Form Row and Input Borders */
        .form-row {
            border: 1px solid #e1e1e1; /* Subtle border around form rows */
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        /* Add Hover Effect to Form Fields */
        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="number"]:hover,
        input[type="date"]:hover,
        select:hover,
        textarea:hover {
            border-color: #4CAF50;
        }

        /* Styling for Specific Inputs */
        input[type="text"].highlighted,
        select.highlighted,
        textarea.highlighted {
            border-color: #3498db;
            background-color: #eef7fd;
        }
        .dashboard-btn {
        color:white;
        background-color:  #04c9f5;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

.dashboard-btn:hover {
    color:white;
    background-color: blue;
}

</style>
<body>
<div class="container">
        <h2>Update Patient and Medical Records</h2>
        <a href="ehr_main.php" class="dashboard-btn">Go to Dashboard</a>
        <form action="submit.php" method="POST">


        <div class="section">
            <!-- Patient Information -->
            <h3>Patient Information</h3>
            <div class="form-row">
                <div class="form-row">

              
                <div class="form-column">
                    <h2>Personal Details</h2>
                    <label for="patient_id">Patient ID (read-only):</label>
                    <input type="text" name="patient_id" value="<?php echo htmlspecialchars($patient['patient_id'] ?? ''); ?>" readonly>

                    <label for="login_id">Login ID (Read-only):</label>
                    <input type="text" name="login_id" value="<?php echo htmlspecialchars($patient['login_id'] ?? ''); ?>" readonly>

                    <label for="firstName">First Name:</label>
                    <input type="text" name="firstName" value="<?php echo htmlspecialchars($patient['firstName'] ?? ''); ?>" readonly>

                    <label for="lastName">Last Name:</label>
                    <input type="text" name="lastName" value="<?php echo htmlspecialchars($patient['lastName'] ?? ''); ?>" readonly>

                    <label for="preferredName">Preferred Name:</label>
                    <input type="text" name="preferredName" value="<?php echo htmlspecialchars($patient['preferredName'] ?? ''); ?>">

                    <label for="preferredPronouns">Preferred Pronouns:</label>
                    <input type="text" name="preferredPronouns" value="<?php echo htmlspecialchars($patient['preferredPronouns'] ?? ''); ?>">

                    <label for="gender">Gender:</label>
                    <select name="gender">
                        <option value="male" <?php if(isset($patient['gender']) && $patient['gender'] == 'male') echo 'selected'; ?>>Male</option>
                        <option value="female" <?php if(isset($patient['gender']) && $patient['gender'] == 'female') echo 'selected'; ?>>Female</option>
                        <option value="other" <?php if(isset($patient['gender']) && $patient['gender'] == 'other') echo 'selected'; ?>>Other</option>
                    </select>

                    <label for="age">Age:</label>
                    <input type="number" name="age" value="<?php echo htmlspecialchars($patient['age'] ?? ''); ?>" required>
              
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($patient['dob'] ?? ''); ?>" required>
                   
                    <label for="maritalStatus">Marital Status:</label>
                    <input type="text" name="maritalStatus" value="<?php echo htmlspecialchars($patient['maritalStatus'] ?? ''); ?>" required>

                    </div>
                    <div class="form-column">
                    <h2>Patient Employment Information</h2>

                    <label for="occupation">Occupation:</label>
                    <input type="text" name="occupation" value="<?php echo htmlspecialchars($medical_records['occupation'] ?? ''); ?>">

                    <label for="job_position">Job Position:</label>
                    <input type="text" name="job_position" value="<?php echo htmlspecialchars($patient['job_position'] ?? ''); ?>">

                    <label for="employment_status">Employment Status:</label>
                    <input type="text" name="employment_status" value="<?php echo htmlspecialchars($medical_records['employment_status'] ?? ''); ?>">

                    <label for="company_name">Company Name:</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($medical_records['company_name'] ?? ''); ?>">

                    <label for="industry">Industry:</label>
                    <input type="text" name="industry" value="<?php echo htmlspecialchars($patient['industry'] ?? ''); ?>">

                    <label for="company_name">Company Name:</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($medical_records['company_name'] ?? ''); ?>">

                    <label for="company_address">Company Address:</label>
                    <input type="text" name="company_address" value="<?php echo htmlspecialchars($medical_records['company_address'] ?? ''); ?>">

                    <label for="company_city">Company City:</label>
                    <input type="text" name="company_city" value="<?php echo htmlspecialchars($medical_records['company_city'] ?? ''); ?>">

                    <label for="company_state">Company State:</label>
                    <input type="text" name="company_state" value="<?php echo htmlspecialchars($medical_records['company_state'] ?? ''); ?>">

                    <label for="company_zip">Company Zip:</label>
                    <input type="text" name="company_zip" value="<?php echo htmlspecialchars($medical_records['company_zip'] ?? ''); ?>">

                </div>

                </div>
                <div class="form-column">
                    <h2>Contact Information</h2>
                    <label for="contact_information">Contact Information:</label>
                    <input type="text" name="contact_information" value="<?php echo htmlspecialchars($patient['contact_information'] ?? ''); ?>" required>     
                    
                    <label for="contactPreference">Contact Preference:</label>
                    <input type="text" name="contactPreference" value="<?php echo htmlspecialchars($patient['contactPreference'] ?? ''); ?>">
                    

                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($patient['email'] ?? ''); ?>" required>
             
                    <label for="address">Address:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($patient['address'] ?? ''); ?>" required>

                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['phone'] ?? ''); ?>" required>
                </div>

                <div class="form-column">
                    <h2>Emergency Contact Information</h2>
                    <label for="emergencyContactName">Emergency Contact Name:</label>
                    <input type="text" name="emergencyContactName" value="<?php echo htmlspecialchars($patient['emergencyContactName'] ?? ''); ?>" required>


                    <label for="relationship">Emergency Contact Relationship:</label>
                    <input type="text" name="relationship" value="<?php echo htmlspecialchars($patient['relationship'] ?? ''); ?>" required>

                    <label for="emergencyContactNumber">Emergency Contact Number:</label>
                    <input type="text" name="emergencyContactNumber" value="<?php echo htmlspecialchars($patient['emergencyContactNumber'] ?? ''); ?>" required>
               

                </div>

      
            </div>
        </div>

       
        <div class="section medical-records">
            <h3>Medical Records Information</h3>
            <div class="form-row">
            <div class="form-column">
                 
                 
                             <h2>Vitals</h2>
                             
                             <label for="blood_pressure">Blood Pressure:</label>
                             <input type="text" name="blood_pressure" value="<?php echo htmlspecialchars($medical_records['blood_pressure'] ?? ''); ?>">
         
                             <label for="heart_rate">Heart Rate:</label>
                             <input type="text" name="heart_rate" value="<?php echo htmlspecialchars($medical_records['heart_rate'] ?? ''); ?>">
         
                             <label for="temperature">Temperature:</label>
                             <input type="number" step="0.01" name="temperature" value="<?php echo htmlspecialchars($medical_records['temperature'] ?? ''); ?>">
         
                             <label for="weight">Weight:</label>
                             <input type="number" step="0.01" name="weight" value="<?php echo htmlspecialchars($medical_records['weight'] ?? ''); ?>">
         
                             <label for="height">Height:</label>
                             <input type="number" step="0.01" name="height" value="<?php echo htmlspecialchars($medical_records['height'] ?? ''); ?>">
                            
                      </div>
                <div class="form-column">
                    <h2>Visitation Details</h2>
                    <label for="record_id">Record ID (read-only):</label>
                    <input type="text" name="record_id" value="<?php echo htmlspecialchars($medical_records['record_id'] ?? ''); ?>" readonly>
                   
                    <label for="visit_date">Visit Date:</label>
                    <input type="date" name="visit_date" value="<?php echo htmlspecialchars($medical_records['visit_date'] ?? ''); ?>" required>

                    <label for="visit_reason">Visit Reason:</label>
                    <input type="text" name="visit_reason" value="<?php echo htmlspecialchars($medical_records['visit_reason'] ?? ''); ?>" required>
             
                
                 </div>
                    <div class="form-column">
                        <h2>Diagnosis & Treatment</h2>
                        <label for="diagnosis">Diagnosis:</label>
                    <textarea name="diagnosis"><?php echo htmlspecialchars($medical_records['diagnosis'] ?? ''); ?></textarea>

                    <label for="treatment_plans">Treatment Plans:</label>
                    <textarea name="treatment_plans"><?php echo htmlspecialchars($medical_records['treatment_plans'] ?? ''); ?></textarea>

                    <label for="test_results">Test Results:</label>
                    <textarea name="test_results"><?php echo htmlspecialchars($medical_records['test_results'] ?? ''); ?></textarea>
                  
        </div>
        
        </div>         
                <div class="form-row">
                <div class="form-column">
                    <h2>Physician Information</h2>
                    <label for="primary_physician">Primary Physician:</label>
                    <input type="text" name="primary_physician" value="<?php echo htmlspecialchars($medical_records['primary_physician'] ?? ''); ?>">

                    <label for="primary_physician_address">Primary Physician Address:</label>
                    <input type="text" name="primary_physician_address" value="<?php echo htmlspecialchars($medical_records['primary_physician_address'] ?? ''); ?>">

                    <label for="primary_physician_contact">Primary Physician Contact:</label>
                    <input type="text" name="primary_physician_contact" value="<?php echo htmlspecialchars($medical_records['primary_physician_contact'] ?? ''); ?>">
                   
                    <label for="secondary_physician">Secondary Physician:</label>
                    <input type="text" name="secondary_physician" value="<?php echo htmlspecialchars($medical_records['secondary_physician'] ?? ''); ?>">

                    <label for="secondary_physician_contact">Secondary Physician Contact:</label>
                    <input type="text" name="secondary_physician_contact" value="<?php echo htmlspecialchars($medical_records['secondary_physician_contact'] ?? ''); ?>">
                 
                </div>

             

                <div class="form-column">
                    <h2>Pregnancy Information</h2>
                    <label for="pregnancy_status">Pregnancy Status:</label>
                    <select name="pregnancy_status">
                        <option value="Yes" <?php if(isset($medical_records['pregnancy_status']) && $medical_records['pregnancy_status'] == 'Yes') echo 'selected'; ?>>Yes</option>
                        <option value="No" <?php if(isset($medical_records['pregnancy_status']) && $medical_records['pregnancy_status'] == 'No') echo 'selected'; ?>>No</option>
                        <option value="N/A" <?php if(isset($medical_records['pregnancy_status']) && $medical_records['pregnancy_status'] == 'N/A') echo 'selected'; ?>>N/A</option>
                    </select>

                    <label for="pregnancy_duration">Pregnancy Duration (if applicable):</label>
                    <input type="text" name="pregnancy_duration" value="<?php echo htmlspecialchars($medical_records['pregnancy_duration'] ?? ''); ?>">


                </div>
                <div class="form-column">
                        <h2>Insurance Details</h2>
                 <label for="insurance_carrier">Insurance Carrier:</label>
                    <input type="text" name="insurance_carrier" value="<?php echo htmlspecialchars($medical_records['insurance_carrier'] ?? ''); ?>">

                    <label for="insurance_plan">Insurance Plan:</label>
                    <input type="text" name="insurance_plan" value="<?php echo htmlspecialchars($medical_records['insurance_plan'] ?? ''); ?>">

                    <label for="insurance_contact">Insurance Contact:</label>
                    <input type="text" name="insurance_contact" value="<?php echo htmlspecialchars($medical_records['insurance_contact'] ?? ''); ?>">

                    <label for="policy_number">Policy Number:</label>
                    <input type="text" name="policy_number" value="<?php echo htmlspecialchars($medical_records['policy_number'] ?? ''); ?>">
                    
                    <label for="group_number">Group Number:</label>
                    <input type="text" name="group_number" value="<?php echo htmlspecialchars($medical_records['group_number'] ?? ''); ?>">
                    
                    <label for="ssn">Social Security Number:</label>
                    <input type="text" name="ssn" value="<?php echo htmlspecialchars($medical_records['ssn'] ?? ''); ?>">
                </div>

                <div class="form-column">
                   <h2>Lifestyle & Background</h2>
                    <label for="present_symptoms">Present Symptoms:</label>
                    <textarea name="present_symptoms"><?php echo htmlspecialchars($medical_records['present_symptoms'] ?? ''); ?></textarea>

                    <label for="symptom_details">Symptom Details:</label>
                    <textarea name="symptom_details"><?php echo htmlspecialchars($medical_records['symptom_details'] ?? ''); ?></textarea>

    
<label for="symptom_duration">Symptom Duration:</label>
<input type="text" name="symptom_duration" value="<?php echo htmlspecialchars($medical_records['symptom_duration'] ?? ''); ?>">

<label for="symptom_severity">Symptom Severity:</label>
<input type="text" name="symptom_severity" value="<?php echo htmlspecialchars($medical_records['symptom_severity'] ?? ''); ?>">

<label for="exercise_frequency">Exercise Frequency:</label>
<input type="text" name="exercise_frequency" value="<?php echo htmlspecialchars($medical_records['exercise_frequency'] ?? ''); ?>">

<label for="diet">Diet:</label>
<input type="text" name="diet" value="<?php echo htmlspecialchars($medical_records['diet'] ?? ''); ?>">

<label for="sleep_patterns">Sleep Patterns:</label>
<input type="text" name="sleep_patterns" value="<?php echo htmlspecialchars($medical_records['sleep_patterns'] ?? ''); ?>">

<label for="stress_management">Stress Management:</label>
<input type="text" name="stress_management" value="<?php echo htmlspecialchars($medical_records['stress_management'] ?? ''); ?>">

<label for="substance_use">Substance Use:</label>
<input type="text" name="substance_use" value="<?php echo htmlspecialchars($medical_records['substance_use'] ?? ''); ?>">



                    
         </div>
         <div class="form-column">     
                <h2>Medical Information</h2>

                <label for="medical_conditions">Medical Conditions:</label>
                    <textarea name="medical_conditions"><?php echo htmlspecialchars($medical_records['medical_conditions'] ?? ''); ?></textarea>

                    <label for="medications">Medications:</label>
                    <textarea name="medications"><?php echo htmlspecialchars($medical_records['medications'] ?? ''); ?></textarea>
                
                <label for="previous_injuries">Previous Injuries:</label>
                    <textarea name="previous_injuries"><?php echo htmlspecialchars($medical_records['previous_injuries'] ?? ''); ?></textarea>
             
                    <label for="allergies">Allergies:</label>
                    <textarea name="allergies"><?php echo htmlspecialchars($medical_records['allergies'] ?? ''); ?></textarea>
               
                <label for="immunization_history">Immunization History:</label>
                    <textarea name="immunization_history"><?php echo htmlspecialchars($medical_records['immunization_history'] ?? ''); ?></textarea>

                    <label for="family_medical_history">Family Medical History:</label>
                    <textarea name="family_medical_history"><?php echo htmlspecialchars($medical_records['family_medical_history'] ?? ''); ?></textarea>

                </div>
                 
            </div>
            </div>
        </div>

            <button type="submit">Update Records</button>
        </form>
    </div>

    <script>
document.getElementById('employeeForm').addEventListener('submit', function(event) {
    let firstName = document.getElementById('firstName').value;
    let age = document.getElementById('age').value;
    let email = document.getElementById('email').value;

    if (!name || !age || !email) {
        event.preventDefault();
        alert('All fields are required.');
    }
});
</script>
</body>
</html>
