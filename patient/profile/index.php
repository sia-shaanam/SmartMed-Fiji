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


// Fetch patient's information based on login_id from session
$login_id = $_SESSION['login_id'];

// Check if the patient_id is set in session
if (isset($_SESSION['patient_id'])) {
    $patient_id = $_SESSION['patient_id'];

    // Prepare the SQL query to fetch the patient data
    $sql = "SELECT * FROM patient WHERE patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $patient_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();
        } else {
            echo "No patient found with this ID.";
        }
    } else {
        echo "Error executing query: " . $conn->error;
    }
} else {
    echo "No patient ID found in session.";
}
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

.con {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: white;
        }

        .form-container {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px; /* Space between columns */
}

.form-group {
    margin-bottom: 15px;
}

.form-group.full-width {
    grid-column: span 3; /* Makes a form element span all columns */
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #1abc9c;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
}


        .form-group input[type="file"] {
            border: none;
        }

        .form-group input[type="submit"] {
            background-color: #00aaff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            transition: background-color 0.3s ease;
        }

        .form-group input[type="submit"]:hover {
            background-color: #003d80;
        }
        
        .optional {
            color: #999;
            font-size: 12px;
        }

        .form-group input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
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
                    <li><a href="../appointment/app_index.php"><span class="fas fa-calendar-check"></span> <small>Appointments</small></a></li>
                    <li><a href="../my_records/my_ehr.php"><span class="fas fa-file-medical"></span> <small>Medical Records</small></a></li>
                    <li><a href="../press/press_index.php"><span class="fas fa-prescription-bottle-alt"></span> <small>Prescriptions</small></a></li>
                    <li><a href="index.php"><span class="fas fa-user-alt"></span> <small>Profile</small></a></li>
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
                <h2>Update Your Profile</h2>
                <div class="user-info">
                   
                    <div class="user-name">
                        <p>Welcome, <?php echo htmlspecialchars($firstName); ?></p>
                    </div>
                </div>
            </div>
        </header>

        <div class="con">
    
    <?php if (isset($patient)): ?>
    <form action="update_patient.php" method="POST" enctype="multipart/form-data">
    <div class="form-container">
    <div class="form-group">
        <label for="patient_id">Patient ID</label>
        <input type="text" id="patient_id" name="patient_id" value="<?php echo htmlspecialchars($patient['patient_id']); ?>" readonly>
    </div>

    <div class="form-group">
        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($patient['firstName']); ?>" required>
    </div>

    <div class="form-group">
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($patient['lastName']); ?>" required>
    </div>

    <div class="form-group">
        <label for="preferredName">Preferred Name <span class="optional">(Optional)</span></label>
        <input type="text" id="preferredName" name="preferredName" value="<?php echo isset($patient['preferredName']) ? htmlspecialchars($patient['preferredName']) : ''; ?>">
    </div>

    <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($patient['dob']); ?>" required>
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="male" <?php echo $patient['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo $patient['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?php echo $patient['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="preferredPronouns">Preferred Pronouns <span class="optional">(Optional)</span></label>
        <input type="text" id="preferredPronouns" name="preferredPronouns" value="<?php echo isset($patient['preferredPronouns']) ? htmlspecialchars($patient['preferredPronouns']) : ''; ?>">
    </div>

    <div class="form-group">
        <label for="maritalStatus">Marital Status</label>
        <input type="text" id="maritalStatus" name="maritalStatus" value="<?php echo htmlspecialchars($patient['maritalStatus']); ?>" required>
    </div>

    <div class="form-group full-width">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($patient['address']); ?>" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
    </div>

    <div class="form-group">
        <label for="contactPreference">Contact Preference <span class="optional">(Optional)</span></label>
        <input type="text" id="contactPreference" name="contactPreference" value="<?php echo isset($patient['contactPreference']) ? htmlspecialchars($patient['contactPreference']) : ''; ?>">
    </div>

    <div class="form-group full-width">
        <label for="emergencyContactName">Emergency Contact Name</label>
        <input type="text" id="emergencyContactName" name="emergencyContactName" value="<?php echo htmlspecialchars($patient['emergencyContactName']); ?>" required>
    </div>

    <div class="form-group">
        <label for="relationship">Relationship to Emergency Contact</label>
        <input type="text" id="relationship" name="relationship" value="<?php echo htmlspecialchars($patient['relationship']); ?>" required>
    </div>

    <div class="form-group">
        <label for="emergencyContactNumber">Emergency Contact Number</label>
        <input type="text" id="emergencyContactNumber" name="emergencyContactNumber" value="<?php echo htmlspecialchars($patient['emergencyContactNumber']); ?>" required>
    </div>

    <div class="form-group full-width">
        <input type="submit" value="Update Profile">
    </div>
</div>

    </form>
    <?php else: ?>
        <p>No patient data available.</p>
    <?php endif; ?>
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
