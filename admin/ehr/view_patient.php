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
    <title>Profile Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color:  #f4f4f4;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        header {
            background-color:  #003366;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        header .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .user-info .user-name {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .user-details {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .dashboard-btn {
        color:black;
        background-color: white;
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
    background-color: black;
}
    section{
        padding:20px;
    }

    .details-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .contact-details {
        flex: 2;
        padding:5px;
        padding-right: 20px;
    }

    .contact-details p {
        margin: 5px 0;
    }

    .button-group {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .button-group button {
        background-color: #003366;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .info-card h3 p{
        padding:20px;

    }

    .button-group button:hover {
        background-color: #17a2b8;
    }

    @media (max-width: 768px) {
        .details-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .button-group {
            width: 100%;
            flex-direction: row;
            justify-content: space-between;
        }
    }
     

        .profile-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-card {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 300px;
        }

        .info-card h3 {
            color: #20c997;
            padding:5x;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table th, .info-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .info-table th {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .info-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        @media (max-width: 768px) {
            .user-details, .info-card {
                width: 100%;
            }

            .profile-section {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <header>
    <div class="user-info">
        <span class="user-name"><?php echo $patient['firstName']; ?></span>
        <a href="ehr_main.php" class="dashboard-btn">Go to Dashboard</a> <!-- Button added here -->
    </div>
</header>


        <div class="user-details">
    <div class="details-wrapper">
        <div class="contact-details">
            <p><strong>Date of Birth:</strong> <?php echo isset($patient['dob']) ? $patient['dob'] : 'DOB not provided'; ?></p>
            <p><strong>Gender:</strong> <?php echo isset($patient['gender']) ? $patient['gender'] : 'Gender not provided'; ?></p>
            <p><strong>Email:</strong> <?php echo $patient['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $patient['phone']; ?></p>
            <p><strong>ID:</strong> <?php echo $patient['patient_id']; ?></p>
        </div>
        <div class="button-group">
            <a href="reset_pass/password_reset.html">
            <button>Re-set Password</button></a>
            <button onclick="window.location.href='update_pat.php?patient_id=<?php echo $patient['patient_id']; ?>'">Update Patient</button>
     <!-- Add a button for messaging -->
    <button onclick="window.location.href='message_patient.php?patient_id=<?php echo $patient['patient_id']; ?>'">Message</button>
</div>

        </div>
    </div>
</div>
        <main>
            <section class="profile-section">
                <div class="info-card">
                    <h3>Personal Details</h3>
                    <p><strong>Age:</strong> <?php echo $patient['age']; ?></p>
                    <p><strong>Preferred Name:</strong> <?php echo $patient['preferredName']; ?></p>
                    <p><strong>Marital Status:</strong> <?php echo $patient['maritalStatus']; ?></p>
                    <p><strong>Preferred Pronouns:</strong> <?php echo $patient['preferredPronouns']; ?></p>
                    <p><strong>Address:</strong> <?php echo $patient['address']; ?></p>
                    <p><strong>Emergency Contact:</strong> <?php echo $patient['emergencyContactName'] . ' (' . $patient['relationship'] . ')'; ?></p>
                    <p><strong>Emergency Contact Number:</strong> <?php echo $patient['emergencyContactNumber']; ?></p>
                </div>

                <div class="info-card">
                    <h3>Vitals</h3>
                    <p><strong>Blood Pressure:</strong> <?php echo $medical_records['blood_pressure']; ?></p>
                    <p><strong>Heart Rate:</strong> <?php echo $medical_records['heart_rate']; ?></p>
                    <p><strong>Temperature:</strong> <?php echo $medical_records['temperature']; ?></p>
                    <p><strong>Weight:</strong> <?php echo $medical_records['weight']; ?></p>
                    <p><strong>Height:</strong> <?php echo $medical_records['height']; ?></p>
                    <p><strong>Diagnosis:</strong> <?php echo $medical_records['diagnosis']; ?></p>
                </div>
            </section>


                <!-- Medical and Lifestyle Information -->
                <section>
        <h3>Health History</h3>
        <table class="info-table">
            <tr>
                <th>Category</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Immunization History</td>
                <td><?php echo $medical_records['immunization_history']; ?></td>
            </tr>
            <tr>
                <td>Allergies</td>
                <td><?php echo $medical_records['allergies']; ?></td>
            </tr>
            <tr>
                <td>Previous Injuries</td>
                <td><?php echo $medical_records['previous_injuries']; ?></td>
            </tr>
            <tr>
                <td>Family Medical History</td>
                <td><?php echo $medical_records['family_medical_history']; ?></td>
            </tr>
        </table>

        <h3>Lifestyle Information</h3>
        <table class="info-table">
            <tr>
                <th>Category</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Present Symptoms</td>
                <td><?php echo $medical_records['present_symptoms']; ?></td>
            </tr>
            <tr>
                <td>Symptom Details</td>
                <td><?php echo $medical_records['symptom_details']; ?></td>
            </tr>
            <tr>
                <td>Symptom Duration</td>
                <td><?php echo $medical_records['symptom_duration']; ?></td>
            </tr>
            <tr>
                <td>Symptom Severity</td>
                <td><?php echo $medical_records['symptom_severity']; ?></td>
            </tr>
            <tr>
                <td>Exercise Frequency</td>
                <td><?php echo $medical_records['exercise_frequency']; ?></td>
            </tr>
            <tr>
                <td>Diet</td>
                <td><?php echo $medical_records['diet']; ?></td>
            </tr>
            <tr>
                <td>Sleep Patterns</td>
                <td><?php echo $medical_records['sleep_patterns']; ?></td>
            </tr>
            <tr>
                <td>Stress Management</td>
                <td><?php echo $medical_records['stress_management']; ?></td>
            </tr>
            <tr>
                <td>Substance Use</td>
                <td><?php echo $medical_records['substance_use']; ?></td>
            </tr>
        </table>

        <h3>Insurance Information</h3>
        <table class="info-table">
            <tr>
                <th>Category</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Insurance Carrier</td>
                <td><?php echo $medical_records['insurance_carrier']; ?></td>
            </tr>
            <tr>
                <td>Insurance Plan</td>
                <td><?php echo $medical_records['insurance_plan']; ?></td>
            </tr>
            <tr>
                <td>Policy Number</td>
                <td><?php echo $medical_records['policy_number']; ?></td>
            </tr>
            <tr>
                <td>Group Number</td>
                <td><?php echo $medical_records['group_number']; ?></td>
            </tr>
        </table>

        <h3>Physician Information</h3>
        <table class="info-table">
            <tr>
                <th>Category</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Primary Physician</td>
                <td><?php echo $medical_records['primary_physician']; ?></td>
            </tr>
            <tr>
                <td>Primary Physician Contact</td>
                <td><?php echo $medical_records['primary_physician_contact']; ?></td>
            </tr>
            <tr>
                <td>Secondary Physician</td>
                <td><?php echo $medical_records['secondary_physician']; ?></td>
            </tr>
            <tr>
                <td>Secondary Physician Contact</td>
                <td><?php echo $medical_records['secondary_physician_contact']; ?></td>
            </tr>
        </table>

        <h3>Employment Information</h3>
        <table class="info-table">
            <tr>
                <th>Category</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Employment Status</td>
                <td><?php echo $medical_records['employment_status']; ?></td>
            </tr>
            <tr>
                <td>Occupation</td>
                <td><?php echo $medical_records['occupation']; ?></td>
            </tr>
            <tr>
                <td>Company</td>
                <td><?php echo $medical_records['company_name']; ?></td>
            </tr>
            <tr>
                <td>Company Address</td>
                <td><?php echo $medical_records['company_address']; ?></td>
            </tr>
            <tr>
                <td>City</td>
                <td><?php echo $medical_records['company_city']; ?></td>
            </tr>
            <tr>
                <td>State</td>
                <td><?php echo $medical_records['company_state']; ?></td>
            </tr>
        </table>
    </section>   
            </section>
        </main>
    </div>
</body>
</html>
