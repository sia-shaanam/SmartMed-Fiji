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

// Retrieve logged-in patient's id from session
$patient_id = $_SESSION['patient_id'];

// Query to fetch all appointments for the logged-in patient
$query = "SELECT appointment_id, patient_name, appointment_date, appointment_time, doctor_id, status, reason, remark 
          FROM appointments 
          WHERE patient_id = '$patient_id'";

// Execute the query
$result = mysqli_query($conn, $query);


// Retrieve logged-in patient's id from session
$patient_id = $_SESSION['patient_id'];

// Get today's date
$current_date = date('Y-m-d');

// Query to count total confirmed upcoming appointments
$query = "SELECT COUNT(*) AS total_upcoming 
          FROM appointments 
          WHERE patient_id = '$patient_id' 
          AND status = 'Confirmed' 
          AND appointment_date >= '$current_date'";

// Execute the query
$result = mysqli_query($conn, $query);

// Fetch the total number of confirmed upcoming appointments
$row = mysqli_fetch_assoc($result);
$total_upcoming = $row['total_upcoming'];

// Close database connection
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
   
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f4f6f9;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #35495e;
            padding: 20px;
            display: flex;
            flex-direction: column;
            color: #fff;
            position: fixed;
            height: 100vh;
            transition: width 0.3s ease;
            
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .side-header {
            padding: 20px;
            text-align: center;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-pic {
            width: 60px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
            object-fit: cover;
        }

        .profile-section h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .profile-section p {
            font-size: 14px;
            color: #bdc3c7;
        }

        .side-menu ul {
            list-style: none;
            list-style-type: none;
            padding: 0;
        }

        .side-menu ul li {
            margin-bottom: 20px;
            padding: 10px;
            text-align: left;
        }

        .side-menu ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        
        .side-menu ul li a:hover {
            background-color: #1abc9c;
        }

        .side-menu ul li a span {
            margin-right: 10px;
            font-size: 18px;
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

        .sidebar.collapsed .side-menu ul li a span {
            margin-right: 0;
        }

        .sidebar.collapsed .profile-section h1,
        .sidebar.collapsed .profile-section p {
            display: none;
        }
        .sidebar.collapsed .side-menu ul li small {
            display: none;
        }


        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background-color: #fff;
    border-bottom: 2px solid #ecf0f1;
}

.header-content h2 {
    font-size: 24px;
    color: #333;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-info span {
    margin-left: 20px;
    font-size: 18px;
    color: #7f8c8d;
    cursor: pointer;
}

.user-name p {
    font-size: 16px;
    margin-left: 10px;
    color: #7f8c8d;
}

       
/* Dashboard Overview */
.overview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.card {
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.card h3 {
    margin-bottom: 10px;
    color: #333;
}

.card p {
    margin-bottom: 20px;
    color: #7f8c8d;
}

.card a {
    display: inline-block;
    padding: 10px 20px;
    background-color: #1abc9c;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.card a:hover {
    background-color: #00aaff;
}

/* Patient Info Section */
.patient-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    margin-top: 20px;
}

.patient-card,
.notes-section {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    flex: 1;
    text-align: center;
}

.patient-card img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
}

.notes-section {
    text-align: left;
}

/* Files/Payments Section */
.files-payments {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    margin-top: 10px;
}

.files,
.payments {
    background-color: white;
    padding: 10px;
    border-radius: 10px;
    flex: 1;
}

/* Table Styles */
.container {
    width: 100%;
    max-width: 1200px;
    margin: 10px auto;
    padding: 10px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table,
th,
td {
    border: 1px solid #e0e0e0;
}

th,
td {
    padding: 12px 15px;
    text-align: left;
}

th {
    background-color: #3498db;
    color: #fff;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #20c997;
}

.no-data {
    text-align: center;
    padding: 20px;
    font-size: 18px;
    color: #888;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .main-content {
        margin-left: 80px;
    }

    .sidebar.collapsed {
        width: 60px;
    }

    .main-content.collapsed {
        margin-left: 60px;
    }

    .patient-info,
    .files-payments {
        flex-direction: column;
    }
}
.files h1{
    margin-bottom: 20px;
}
.download-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: lightseagreen; /* Green */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .download-button:hover {
            background-color: #45a049;
        }
        .down {
            padding: 10px 20px;
            font-size: 16px;
            background-color: lightseagreen; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }
        .down:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Sidebar Menu -->
    <input type="checkbox" id="menu-toggle" onclick="toggleSidebar()">
    <div class="sidebar" id="sidebar">
        <label for="menu-toggle"><span class="fas fa-bars"></span></label>
        <div class="side-header">
        </div>
        <div class="side-content">
            <!-- Profile Section -->
            <div class="profile-section">
                <img src="../image/a.webp" alt="Profile Picture" class="profile-pic">
                <h1><?php echo htmlspecialchars($firstName); ?></h1>
                <p><?php echo 'ID: ' . htmlspecialchars($patient_id); ?></p>
            </div>
            <!-- Sidebar Menu -->
            <div class="side-menu">
                <ul>
                    <li><a href="../pdashboard.php" ><span class="fas fa-home"></span> <small>Dashboard</small></a></li>
                    <li><a href="app_index.php"><span class="fas fa-calendar-check"></span> <small>Appointments</small></a></li>
                    <li><a href="../my_records/my_ehr.php"><span class="fas fa-file-medical"></span> <small>Medical Records</small></a></li>
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
                <h2>My Appointment Mangement Center</h2>
                <div class="user-info">
                   
                    <div class="user-name">
                        <p>Welcome, <?php echo htmlspecialchars($firstName); ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Patient Info Section -->
        <section class="patient-info">
            <div class="patient-card">
            <img src="../image/a.webp" alt="Profile Picture" class="profile-pic">
             
                
                <div class="appointments">
                <h1>Total Confirmed Upcoming Appointments</h1>
                <p><strong><?php echo $total_upcoming; ?></strong> </p>
                
                </div>
            </div>
            <div class="notes-section">
                <h4>Notes</h4>
                <p>1. Request doctors for a video chat through message.</p>
                <p>2. Doctors will send a link.</p>
                <p>3. Copy & Paste the <em>Link</em> in google and join through your email.</p>
           </div>
        </section>

        <!-- Files/Payments Section -->
        <section class="files-payments">
            <div class="files">
            <h1>Download Your Appointment Timeline</h1>
    <a href="generate_appointments_pdf.php" class="download-button">Download PDF</a>
        <a href="add-appointment.php">
                <button class="down">Add New Appointments</button>
                </a>
                

         </div>

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
        echo "<table>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Doctor ID</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Remark</th>
                </tr>";

        // Loop through the results and display them in a table
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $row['appointment_id'] . "</td>
                    <td>" . $row['patient_name'] . "</td>
                    <td>" . $row['appointment_date'] . "</td>
                    <td>" . $row['appointment_time'] . "</td>
                    <td>" . $row['doctor_id'] . "</td>
                    <td>" . $row['status'] . "</td>
                    <td>" . $row['reason'] . "</td>
                    <td>" . $row['remark'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-data'>No confirmed appointments found from today onwards.</div>";
    }

    // Close database connection
    mysqli_close($conn);
    ?>

</div>

            </div>
        </section>
        <?php
// Include database connection
include('smf_db_conn.php');



// Retrieve logged-in patient's id from session
$patient_id = $_SESSION['patient_id'];

// Query to fetch all appointments for the logged-in patient
$query = "SELECT appointment_id, patient_name, appointment_date, appointment_time, doctor_id, status, reason, remark 
          FROM appointments 
          WHERE patient_id = '$patient_id'";

// Execute the query
$result = mysqli_query($conn, $query);

// Start HTML
?>
        <div class="container">
    <h1>My Appointments History</h1>

    <?php
    // Check if there are any appointments
    if (mysqli_num_rows($result) > 0) {
        echo "<table>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Doctor ID</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Remark</th>
                </tr>";

        // Loop through the results and display them in a table
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $row['appointment_id'] . "</td>
                    <td>" . $row['patient_name'] . "</td>
                    <td>" . $row['appointment_date'] . "</td>
                    <td>" . $row['appointment_time'] . "</td>
                    <td>" . $row['doctor_id'] . "</td>
                    <td>" . $row['status'] . "</td>
                    <td>" . $row['reason'] . "</td>
                    <td>" . $row['remark'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-data'>No appointments found.</div>";
    }

    // Close database connection
    mysqli_close($conn);
    ?>

</div>

     
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        }


    
    </script>
</body>
</html>
