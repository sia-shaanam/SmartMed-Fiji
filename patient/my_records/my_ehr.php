
<?php
session_start();
include('smf_db_conn.php');

// Check if the user is logged in
if (!isset($_SESSION['login_id'])) {
    echo 'No login_id found in the session.';
    header('Location: login.php');
    exit(); // Ensure the script stops after redirection
}

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Fetch patient's information based on login_id from session
$login_id = $_SESSION['login_id'];
$sql = "SELECT firstName, patient_id FROM patient WHERE login_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle statement preparation error
    error_log("Query preparation failed: " . $conn->error);
    header('Location: error.php'); // Redirect to a friendly error page
    exit();
}

$stmt->bind_param("s", $login_id);
$stmt->execute();
$stmt->bind_result($firstName, $patient_id);

// Check if query returns any results
if ($stmt->fetch()) {
    // Store patient_id in the session
    $_SESSION['patient_id'] = $patient_id; // Set patient_id in the session
} else {
    echo 'No data found for this login_id: ' . htmlspecialchars($login_id); // Debugging line
    exit(); // Stop execution if no patient data is found
}

$stmt->close(); // Close the statement after fetching the first result

// Now retrieve the patient details using patient_id from the session
$patient_id = $_SESSION['patient_id']; // Use patient_id from session
$sql = "SELECT * FROM patient WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    // Handle case where patient is not found
    echo "No patient found.";
    exit(); // Stop execution if no patient is found
}

$sql = "SELECT * FROM medical_records WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $medicalRecords = $result->fetch_assoc();
} else {
    // Handle no records found case
    $medicalRecords = [];
}

$conn->close(); // Close the database connection
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Patient Dashboard | SmartMed Fiji</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100%;
            background-color: #35495e;
            color: white;
            position: fixed;
            transition: width 0.3s ease;
        }

        /* Collapsed sidebar */
        .sidebar.collapsed {
            width: 80px;
        }

        /* Sidebar content */
        .side-header {
            padding: 20px;
            text-align: center;
        }

        .header-content h3 span {
            color: #1abc9c;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-pic {
            border-radius: 50%;
            width: 60px;
            height: 60px;
        }

        .side-menu ul {
            list-style-type: none;
            padding: 0;
        }

        .side-menu ul li {
            padding: 10px;
            text-align: left;
        }

        .side-menu ul li a {
            color: white;
            text-decoration: none;
            display: block;
            transition: background 0.2s ease;
        }

        .side-menu ul li a:hover {
            background-color: #1abc9c;
        }

        /* Styling for toggle functionality */
        #menu-toggle {
            display: none;
        }

        .sidebar label[for="menu-toggle"] {
            position: absolute;
            top: 10px;
            left: 10px;
            cursor: pointer;
            font-size: 20px;
        }

        /* Collapsed state styling for icons only */
        .sidebar.collapsed .side-menu ul li {
            text-align: center;
        }

        .sidebar.collapsed .side-menu ul li small {
            display: none;
        }

        .sidebar.collapsed .profile-section h1,
        .sidebar.collapsed .profile-section p {
            display: none;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        /* Dashboard Overview */
        .overview {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between; /* Distribute space between cards */
        }

        .card {
            flex: 1 1 300px; /* Flex grow, shrink, and base width */
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Profile content styles */
        .profile-content {
            margin-top: 20px; /* Add margin above profile content */
            background-color: #f9f9f9; /* Background for profile section */
            padding: 20px;
            border-radius: 8px;
        }

        .profile-header {
            display: flex; /* Use flexbox for profile layout */
            align-items: center; /* Center items vertically */
            margin-bottom: 20px;
        }

        .profile-photo {
            margin-right: 20px; /* Space between photo and details */
        }

        .tabs {
            display: flex; /* Display tabs in a row */
            margin-bottom: 20px;
        }

        .tab {
            flex: 1; /* Make tabs equal width */
            padding: 10px;
            text-align: center;
            background: #35495e;
            color: white;
            border: none;
            cursor: pointer;
        }

        .tab.active {
            background: #00aaff; /* Active tab style */
        }

        .profile-info {
            display: flex; /* Use flexbox for profile info layout */
            flex-direction: column; /* Stack items vertically */
        }

        .personal-info, .used-drugs, .allergies, .notes {
            margin-bottom: 20px; /* Space between sections */
            background-color: white; /* Background for each section */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <!-- Toggle Checkbox -->
    <input type="checkbox" id="menu-toggle" onclick="toggleSidebar()">
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <label for="menu-toggle"><span class="fas fa-bars"></span></label>
        <div class="side-header"></div>
        <div class="side-content">
            <!-- Profile Section -->
            <div class="profile-section">
            <img src="../image/a.webp" alt="Profile Picture" class="profile-pic">
                <h1><?php echo ' ' . htmlspecialchars($firstName) . '<br>'; ?></h1>
                <p><?php echo 'ID: ' . htmlspecialchars($patient_id) . '<br>'; ?></p>
            </div>

            <!-- Sidebar Menu -->
            <div class="side-menu">
                <ul>
                    <li><a href="../pdashboard.php" ><span class="fas fa-home"></span> <small>Dashboard</small></a></li>
                    <li><a href="../appointment/app_index.php"><span class="fas fa-calendar-check"></span> <small>Appointments</small></a></li>
                    <li><a href="my_ehr.php"><span class="fas fa-file-medical"></span> <small>Medical Records</small></a></li>
                    <li><a href="../press/press_index.php"><span class="fas fa-prescription-bottle-alt"></span> <small>Prescriptions</small></a></li>
                    <li><a href="../profile/index.php"><span class="fas fa-user-alt"></span> <small>Profile</small></a></li>
                    <li><a href="../mess/view_messages.php"><span class="fas fa-envelope"></span> <small>Messages</small></a></li>
                    <li><a href="../logout.php"><span class="fas fa-sign-out-alt"></span> <small>Logout</small></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <header>
            <div class="header-content">
                <h3>SmartMed <span>Fiji</span></h3>
                <h2>My Records(EHR)</h2>
                <div class="user-info">
                 
                    <div class="user-name">
                        <p>Welcome, <?php echo htmlspecialchars($firstName); ?></p>
                    </div>
                </div>
            </div>
        </header>

        <main class="profile-content">
    <header class="profile-header">
        <div class="profile-photo">
            <img src="../image/a.webp" alt="<?php echo htmlspecialchars($patient['preferredName'] ?? $patient['firstName']); ?>">
        </div>
        <div class="profile-details">
            <h2><?php echo htmlspecialchars($patient['preferredName'] ?? $patient['firstName']); ?></h2>
            <p>Gender: <?php echo htmlspecialchars($patient['gender']); ?> | Age: <?php echo htmlspecialchars($patient['age']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($patient['phone']); ?></p>
            <p>Email: <?php echo htmlspecialchars($patient['email']); ?></p>
            <p>Address: <?php echo htmlspecialchars($patient['address']); ?></p>
            <button onclick="window.location.href='generate_pdf.php'" style="
    background-color:  #00aaff;
    border: none;
    color: white;
    padding: 10px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 12px;
    transition: background-color 0.3s ease;
">
    Print My Records(PDF)
</button>

        </div>
    </header>

    <div class="tabs">
        <button class="tab active" onclick="showTab('general')">General</button>
        <button class="tab" onclick="showTab('contact')">Contact Info</button>
        <button class="tab" onclick="showTab('basic')">Identifiers</button>
        <button class="tab" onclick="showTab('vital')">Vital Signs & Physical Measurements</button>
        <button class="tab" onclick="showTab('medical')">Medical Info</button>
        <button class="tab" onclick="showTab('symptoms')">Symptoms & Health Habits</button>
        <button class="tab" onclick="showTab('employment')">Employment</button>
    
    </div>

    <section class="profile-info">
        <!-- General Information -->
        <div id="general" class="tab-content" style="display:block;">
            <h3>General Information</h3>
            <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($patient['patient_id']); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($patient['firstName']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($patient['lastName']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['dob']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
            <p><strong>Preferred Pronouns:</strong> <?php echo htmlspecialchars($patient['preferredPronouns']); ?></p>
            <p><strong>Marital Status:</strong> <?php echo htmlspecialchars($patient['maritalStatus']); ?></p>
        </div>

        <!-- Contact Information -->
        <div id="contact" class="tab-content">
            <h3>Contact Information</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
            <p><strong>Contact Preference:</strong> <?php echo htmlspecialchars($patient['contactPreference']); ?></p>
            <h3>Emergency Contact</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['emergencyContactName']); ?></p>
            <p><strong>Relationship:</strong> <?php echo htmlspecialchars($patient['relationship']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['emergencyContactNumber']); ?></p>
       
        </div>


        <div id="basic" class="tab-content">
        <h3>Identifier</h3>
        <p><strong>Record ID:</strong> <?php echo htmlspecialchars($medicalRecords['record_id']); ?></p>
        <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($medicalRecords['patient_id']); ?></p>

        <h3>Physician Information</h3>
               <p><strong>Primary Physician Name:</strong> <?php echo htmlspecialchars($medicalRecords['primary_physician'] ?? 'N/A'); ?></p>
             <p><strong>Primary Physician Contact:</strong> <?php echo htmlspecialchars($medicalRecords['primary_physician_contact'] ?? 'N/A'); ?></p>
              <p><strong>Primary Physician Address:</strong> <?php echo htmlspecialchars($medicalRecords['primary_physician_address'] ?? 'N/A'); ?></p>
               <p><strong>Secondary Physician Name:</strong> <?php echo htmlspecialchars($medicalRecords['secondary_physician'] ?? 'N/A'); ?></p>
              <p><strong>Secondary Physician Contact:</strong> <?php echo htmlspecialchars($medicalRecords['secondary_physician_contact'] ?? 'N/A'); ?></p>  
            </div>


             <div id="vital" class="tab-content">
             <h3>Vitals</h3>
   <p><strong>Blood Pressure:</strong> <?php echo htmlspecialchars($medicalRecords['blood_pressure'] ?? 'N/A'); ?></p>
    <p><strong>Heart Rate:</strong> <?php echo htmlspecialchars($medicalRecords['heart_rate'] ?? 'N/A'); ?></p>
    <p><strong>Temperature:</strong> <?php echo htmlspecialchars($medicalRecords['temperature'] ?? 'N/A'); ?> °C</p>
    <h3>Physical Measurements</h3>
    <p><strong>Weight:</strong> <?php echo htmlspecialchars($medicalRecords['weight'] ?? 'N/A'); ?> kg</p>
    <p><strong>Height:</strong> <?php echo htmlspecialchars($medicalRecords['height'] ?? 'N/A'); ?> cm</p>
    <p><strong>Pregnancy Status:</strong> <?php echo htmlspecialchars($medicalRecords['pregnancy_status'] ?? 'N/A'); ?> kg</p>
    <p><strong>Pregnancy Duration:</strong> <?php echo htmlspecialchars($medicalRecords['pregnancy_duration'] ?? 'N/A'); ?> cm</p>

</div>

        <!-- Medical Information -->
        <div id="medical" class="tab-content">
            <h3>Medical Information</h3>
            <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($medicalRecords['diagnosis'] ?? 'N/A'); ?></p>
            <p><strong>Treatment Plans:</strong> <?php echo htmlspecialchars($medicalRecords['treatment_plans'] ?? 'N/A'); ?></p>
            <p><strong>Test Results:</strong> <?php echo htmlspecialchars($medicalRecords['test_results'] ?? 'N/A'); ?></p>
            <p><strong>Medical Conditions:</strong> <?php echo htmlspecialchars($medicalRecords['medical_conditions']); ?></p>
            <p><strong>Medications:</strong> <?php echo htmlspecialchars($medicalRecords['medications']); ?></p>
            <p><strong>Allergies:</strong> <?php echo htmlspecialchars($medicalRecords['allergies']); ?></p>
            <p><strong>Immunization History:</strong> <?php echo htmlspecialchars($medicalRecords['immunization_history']); ?></p>
            <p><strong>Pervious Injuries:</strong> <?php echo htmlspecialchars($medicalRecords['previous_injuries']); ?></p>
            <p><strong>Family Medical History:</strong> <?php echo htmlspecialchars($medicalRecords['family_medical_history']); ?></p>
        </div>

        <div id="symptoms" class="tab-content">
    <h3>Symptoms & Health Habits</h3>
    <p><strong>Symptoms:</strong> <?php echo htmlspecialchars($medicalRecords['present_symptoms'] ?? 'N/A'); ?></p>
    <p><strong>Exercise Habits:</strong> <?php echo htmlspecialchars($medicalRecords['exercise_frequency'] ?? 'N/A'); ?></p>
    <p><strong>Diet:</strong> <?php echo htmlspecialchars($medicalRecords['diet'] ?? 'N/A'); ?></p>
    <p><strong>Symptoms Details:</strong> <?php echo htmlspecialchars($medicalRecords['symptom_details'] ?? 'N/A'); ?></p>
    <p><strong>Symptoms Duration:</strong> <?php echo htmlspecialchars($medicalRecords['symptom_duration'] ?? 'N/A'); ?></p>
    <p><strong>Symptoms Severity:</strong> <?php echo htmlspecialchars($medicalRecords['symptom_severity'] ?? 'N/A'); ?></p>
    <p><strong>Sleep patterns:</strong> <?php echo htmlspecialchars($medicalRecords['sleep_patterns'] ?? 'N/A'); ?></p>
    <p><strong>Stress Managements:</strong> <?php echo htmlspecialchars($medicalRecords['stress_management'] ?? 'N/A'); ?></p>
    <p><strong>Substance Abuse& use:</strong> <?php echo htmlspecialchars($medicalRecords['substance_use'] ?? 'N/A'); ?></p>      
           
</div>



<div id="employment" class="tab-content">

   <h3>Employment Information</h3>
    <p><strong>Occupation:</strong> <?php echo htmlspecialchars($medicalRecords['occupation'] ?? 'N/A'); ?></p>
    <p><strong>Employment Status:</strong> <?php echo htmlspecialchars($medicalRecords['employment_status'] ?? 'N/A'); ?></p>
    <p><strong>Employer Name:</strong> <?php echo htmlspecialchars($medicalRecords['company_name'] ?? 'N/A'); ?></p>
    <p><strong>Work Address:</strong> <?php echo htmlspecialchars($medicalRecords['company_address'] ?? 'N/A'); ?></p>
    <p><strong>Company City:</strong> <?php echo htmlspecialchars($medicalRecords['company_city'] ?? 'N/A'); ?></p>
    <p><strong>Company Status:</strong> <?php echo htmlspecialchars($medicalRecords['company_address'] ?? 'N/A'); ?></p>
    <p><strong>Company Zip:</strong> <?php echo htmlspecialchars($medicalRecords['company_zip'] ?? 'N/A'); ?></p>
    <p><strong>Industry:</strong> <?php echo htmlspecialchars($medicalRecords['industry'] ?? 'N/A'); ?></p>

    <h3>Insurance Information</h3>
    <p><strong>Insurance Carrier Name:</strong> <?php echo htmlspecialchars($medicalRecords['insurance_carrier'] ?? 'N/A'); ?></p>
    <p><strong>Insurance Plan:</strong> <?php echo htmlspecialchars($medicalRecords['insurance_plan'] ?? 'N/A'); ?></p>
    <p><strong>Insurance Contact:</strong> <?php echo htmlspecialchars($medicalRecords['insurance_contact'] ?? 'N/A'); ?></p>
    <p><strong>Policy Number:</strong> <?php echo htmlspecialchars($medicalRecords['policy_number'] ?? 'N/A'); ?></p>
    <p><strong>Social Security Number:</strong> <?php echo htmlspecialchars($medicalRecords['ssn'] ?? 'N/A'); ?></p> 
    <p><strong>Group Number:</strong> <?php echo htmlspecialchars($medicalRecords['group_number'] ?? 'N/A'); ?></p>
 </div>





    </section>
</main>

        
    


    </div>

    <!-- JavaScript to handle the toggle functionality -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        }

     

        function showTab(tabName) {
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.style.display = 'none';
        });

        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });

        document.getElementById(tabName).style.display = 'block';
        document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
    }

    // Automatically show the general tab on page load
    document.addEventListener('DOMContentLoaded', () => {
        showTab('general');
    });


    </script>
</body>
</html>
