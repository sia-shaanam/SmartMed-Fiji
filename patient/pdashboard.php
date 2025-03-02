
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

// Retrieve medical records
$sql = "SELECT * FROM medical_records WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$medicalRecordsResult = $stmt->get_result();

$medicalRecords = [];
while ($record = $medicalRecordsResult->fetch_assoc()) {
    $medicalRecords[] = $record;
}

$stmt->close(); // Close the statement after retrieving medical records
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
            gap: 10px;
            justify-content: space-between; /* Distribute space between cards */
        }

        .card {
            flex: 1 1 100px; /* Flex grow, shrink, and base width */
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 10px;;
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
            <img src="image/a.webp" alt="Profile Picture" class="profile-pic">
                <h1><?php echo ' ' . htmlspecialchars($firstName) . '<br>'; ?></h1>
                <p><?php echo 'ID: ' . htmlspecialchars($patient_id) . '<br>'; ?></p>
            </div>

            <!-- Sidebar Menu -->
            <div class="side-menu">
                <ul>
                    <li><a href="pdashboard.php" class="active"><span class="fas fa-home"></span> <small>Dashboard</small></a></li>
                    <li><a href="appointment/app_index.php" ><span class="fas fa-calendar-check"></span> <small>Appointments</small></a></li>
                    <li><a href="my_records/my_ehr.php"><span class="fas fa-file-medical"></span> <small>Medical Records</small></a></li>
                    <li><a href="press/press_index.php"><span class="fas fa-prescription-bottle-alt"></span> <small>Prescriptions</small></a></li>
                    <li><a href="profile/index.php"><span class="fas fa-user-alt"></span> <small>Profile</small></a></li>
                    <li><a href="mess/view_messages.php"><span class="fas fa-envelope"></span> <small>Messages</small></a></li>
                    <li><a href="logout.php"><span class="fas fa-sign-out-alt"></span> <small>Logout</small></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <header>
            <div class="header-content">
                <h3>SmartMed <span>Fiji</span></h3>
                <h2>Patient Dashboard</h2>
                <div class="user-info">
                    
                    <div class="user-name">
                        <p>Welcome, <?php echo htmlspecialchars($firstName); ?></p>
                    </div>
                </div>
            </div>
        </header>

   
       

        
        <!-- Dashboard Overview -->
        <section class="overview">
        <div class="card">
        <main class="profile-content">
    <header class="profile-header">
        
        <div class="profile-details">
            <h2><?php echo htmlspecialchars($patient['preferredName'] ?? $patient['firstName']); ?></h2>
            <p>Gender: <?php echo htmlspecialchars($patient['gender']); ?> | Age: <?php echo htmlspecialchars($patient['age']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($patient['phone']); ?></p>
            <p>Email: <?php echo htmlspecialchars($patient['email']); ?></p>
            <p>Address: <?php echo htmlspecialchars($patient['address']); ?></p>
        </div>
    </header>
</main>
            </div>
            <div class="card">
                <span class="fas fa-calendar-check"></span>
                <h3>Upcoming Appointments</h3>
                <p>View and manage your appointments.</p>
                <a href="appointment/app_index.php">View Appointments</a>
            </div>

            <div class="card">
                <span class="fas fa-file-medical"></span>
                <h3>Medical Records</h3>
                <p>Access your health records.</p>
                <a href="my_records/my_ehr.php">View Records</a>
            </div>

            <div class="card">
                <span class="fas fa-prescription-bottle-alt"></span>
                <h3>Prescriptions</h3>
                <p>Review your active prescriptions.</p>
                <a href="press/press_index.php">View Prescriptions</a>
            </div>
            
        </section>
<div class="card">

<?php
// Include database connection
include('smf_db_conn.php');



// Retrieve logged-in patient's id from session
$patient_id = $_SESSION['patient_id'];

// Get today's date
$current_date = date('Y-m-d');

// Query to fetch only confirmed appointments from today and future
$query = "SELECT appointment_id, patient_name, appointment_date, appointment_time, doctor_id, status, reason, remark 
          FROM appointments 
          WHERE patient_id = '$patient_id' 
          AND status = 'Confirmed' 
          AND appointment_date >= '$current_date'";

// Execute the query
$result = mysqli_query($conn, $query);

// Start HTML
?>
            <div class="payments">
            <div class="container">
    <h1>Your Confirmed Appointments</h1>

    <?php
// Check if there are any appointments
if (mysqli_num_rows($result) > 0) {
    echo "<table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
            <tr style='background-color: #00aaff;'>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Appointment ID</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Patient Name</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Appointment Date</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Appointment Time</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Doctor ID</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Status</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Reason</th>
                <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Remark</th>
            </tr>";

    // Loop through the results and display them in a table
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr style='background-color: #fff;'>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['appointment_id']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['patient_name']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['appointment_date']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['appointment_time']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['doctor_id']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['status']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['reason']) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['remark']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<div class='no-data' style='text-align: center; color: #888; margin-top: 20px;'>No confirmed appointments found from today onwards.</div>";
}

// Close database connection
mysqli_close($conn);
?>


</div>

            </div>
        </section>
</div>

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
