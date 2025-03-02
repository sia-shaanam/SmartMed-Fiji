
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
    <link rel="stylesheet" href="../styles.css">
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
            <img src="../image/a.webp" alt="Profile Picture" class="profile-pic">
                <h1><?php echo ' ' . htmlspecialchars($firstName) . '<br>'; ?></h1>
                <p><?php echo 'ID: ' . htmlspecialchars($patient_id) . '<br>'; ?></p>
            </div>

            <!-- Sidebar Menu -->
            <div class="side-menu">
                <ul>
                    <li><a href="../pdashboard.php" ><span class="fas fa-home"></span> <small>Dashboard</small></a></li>
                    <li><a href="../appointment/app_index.php"><span class="fas fa-calendar-check"></span> <small>Appointments</small></a></li>
                    <li><a href="../my_records/my_ehr.php"><span class="fas fa-file-medical"></span> <small>Medical Records</small></a></li>
                    <li><a href="press_index.php"><span class="fas fa-prescription-bottle-alt"></span> <small>Prescriptions</small></a></li>
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
                <h2>Prescriptions</h2>
                <div class="user-info">
                  
                    <div class="user-name">
                        <p>Welcome, <?php echo htmlspecialchars($firstName); ?></p>
                    </div>
                </div>
            </div>
        </header>

   
       

        
        <!-- Dashboard Overview -->
        <section class="overview">
        <?php

include('smf_db_conn.php');


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

// Retrieve all prescription details for the logged-in patient
$sql = "SELECT p.prescription, p.created_at, d.name AS doctor_name 
        FROM prescriptions p 
        JOIN doctors d ON p.doctor_id = d.doctor_id 
        WHERE p.patient_id = ? 
        ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle statement preparation error
    error_log("Query preparation failed: " . $conn->error);
    header('Location: error.php'); // Redirect to a friendly error page
    exit();
}

$stmt->bind_param("s", $patient_id);
$stmt->execute();
$prescriptionsResult = $stmt->get_result();

// Check if any prescriptions exist for the patient
if ($prescriptionsResult->num_rows > 0) {
    // Display the prescriptions in a table
    echo "
    <html>
    <head>
        <style>
           
            .container {
                max-width: 800px;
                margin: 0 auto;
                background-color: #fff;
                padding: 30px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
            h1 {
                text-align: center;
                color: #007bff;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 12px;
                text-align: left;
            }
            th {
                background-color: #007bff;
                color: white;
            }
            td {
                background-color: #f9f9f9;
            }
            a {
                color: #007bff;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>My Prescriptions</h1>
            <table>
                <tr>
                    <th>Doctor</th>
                    <th>Prescription</th>
                    <th>Date</th>
                </tr>
    ";

    // Fetch and display each prescription
    while ($row = $prescriptionsResult->fetch_assoc()) {
        echo "
            <tr>
                <td>" . htmlspecialchars($row['doctor_name']) . "</td>
                <td>" . nl2br(htmlspecialchars($row['prescription'])) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
            </tr>
        ";
    }

    echo "
            </table>
            <a href='../pdashboard.php'>Back to Dashboard</a>
        </div>
    </body>
    </html>
    ";
} else {
    echo "<p>No prescriptions found for this patient.</p>";
}

$stmt->close(); // Close the statement
$conn->close(); // Close the database connection
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
