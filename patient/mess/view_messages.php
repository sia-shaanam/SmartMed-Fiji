
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

/* Card styles */
.message-card {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    max-width: 600px;
    width: 100%;
    text-align: left;
    margin: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Doctor name and message styling */
.message-sender {
    font-size: 18px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 10px;
}

.message-content {
    font-size: 16px;
    color: #34495e;
    margin-bottom: 10px;
}

/* Timestamp styling */
.message-timestamp {
    font-size: 12px;
    color: #95a5a6;
    text-align: right;
}

/* Button container for chat */
.actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

/* Chat button style */
.chat-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Chat button hover effect */
.chat-btn:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

/* Chat button active effect */
.chat-btn:active {
    background-color: #004085;
    transform: translateY(0);
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
                    <li><a href="../press/press_index.php"><span class="fas fa-prescription-bottle-alt"></span> <small>Prescriptions</small></a></li>
                    <li><a href="../profile/index.php"><span class="fas fa-user-alt"></span> <small>Profile</small></a></li>
                    <li><a href="view_messages.php"><span class="fas fa-envelope"></span> <small>Messages</small></a></li>
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
                <h2>My Messages</h2>
                <div class="user-info">
                   
                    <div class="user-name">
                        <p>Welcome, <?php echo htmlspecialchars($firstName); ?></p>
                    </div>
                </div>
            </div>
        </header>

<?php

// Assuming patient_id is stored in session after login
$patient_id = $_SESSION['patient_id'];

// Database connection (update with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smf_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest message, doctor's name, and doctor ID for the logged-in patient
$sql = "SELECT m.message, m.sent_at, d.name, d.user_id
        FROM messages m
        JOIN doctors d ON m.user_id = d.user_id
        WHERE m.patient_id = ?
        ORDER BY m.sent_at DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Store the fetched latest message and doctor's name
$latest_message = $result->fetch_assoc();

// Close connection
$conn->close();
?>
<div class="message-container">
        <?php if ($latest_message): ?>
            <div class="message-card">
                <p class="message-sender">Message from Dr. <?php echo htmlspecialchars($latest_message['name']); ?></p>
                <p class="message-content"><?php echo htmlspecialchars($latest_message['message']); ?></p>
                <p class="message-timestamp">Sent at: <?php echo htmlspecialchars($latest_message['sent_at']); ?></p>

                <!-- Add chat button -->
                <div class="actions">
                    <button class="chat-btn" onclick="window.location.href='chat.php?patient_id=<?php echo $patient_id; ?>&doctor_id=<?php echo htmlspecialchars($latest_message['user_id']); ?>'">Chat with Doctor</button>
                </div>
            </div>
        <?php else: ?>
            <p>No messages found for this patient.</p>
        <?php endif; ?>
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
