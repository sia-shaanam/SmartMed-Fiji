<?php
// Start the session
// Start the session securely
session_start();

// Check if the user is logged in and has the role 'doctor'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Include the database connection file
include('smf_db_conn.php');

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Prepare and execute the query to fetch the doctor's details
$stmt = $conn->prepare("SELECT name, doctor_id FROM doctors WHERE user_id = ?");
if ($stmt === false) {
    error_log("Database error: " . $conn->error); // Log the error for admin
    die("<script>alert('Database error. Please try again later.');</script>");
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare the SQL query to fetch all doctor details
$sql = "SELECT * FROM doctors WHERE user_id = ?";
// Prepare the statement
$stmt = $conn->prepare($sql);


// Check if exactly one doctor record is found
if ($result->num_rows === 1) {
    $doctor = $result->fetch_assoc(); // Fetch doctor details
    $doctor_name = htmlspecialchars($doctor['name']); // Sanitize output
    $doctor_id = htmlspecialchars($doctor['doctor_id']); // Sanitize output
} else {
    echo "<script>alert('Doctor details not found. Please contact the administrator.');</script>";
    exit();
}





// Ensure $user_id is set (e.g., from session or request)
$user_id = $_SESSION['user_id'];  // Assuming the user_id is stored in the session

// SQL query to count confirmed appointments for the doctor (based on user_id) on today's date
$appointment_sql = "
    SELECT COUNT(a.appointment_id) AS todays_confirmed_appointments
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE d.user_id = ? 
      AND a.appointment_date = CURDATE()
      AND a.status = 'Confirmed'";

// Prepare the SQL statement
$appointment_stmt = $conn->prepare($appointment_sql);

if ($appointment_stmt) {
   $appointment_stmt->bind_param("s", $user_id);
   $appointment_stmt->execute();
   $appointment_stmt->bind_result($todays_confirmed_appointments);
   $appointment_stmt->fetch();
  $appointment_stmt->close();
    
} else {
    die('Error: Failed to prepare appointment query.');
}




// SQL Query: Count total confirmed appointments for the logged-in doctor
$total_confirmed_sql = "
    SELECT COUNT(a.appointment_id) AS total_confirmed_appointments 
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE d.user_id = ? AND a.status = 'Confirmed'";

$total_confirmed_stmt = $conn->prepare($total_confirmed_sql);

if ($total_confirmed_stmt) {
    $total_confirmed_stmt->bind_param("s", $user_id); // 's' because user_id is a string
  $total_confirmed_stmt->execute();
  $total_confirmed_stmt->bind_result($total_confirmed_appointments);
  $total_confirmed_stmt->fetch();
   $total_confirmed_stmt->close();

} else {
    die('Error: Failed to prepare total confirmed appointments query.');
}

// Step 4: Count total patients in the patient table
$total_patients_sql = "SELECT COUNT(*) AS total_patient FROM patient";
$total_patient_stmt = $conn->prepare($total_patients_sql);

if ($total_patient_stmt) {
    $total_patient_stmt->execute();
    $total_patient_stmt->bind_result($total_patient); // Bind result to total_patient
    $total_patient_stmt->fetch();
    $total_patient_stmt->close(); // Close the statement
} else {
    die('Error: Failed to prepare total patient query.');
}


// Prepare the SQL query to fetch all doctor details
$sql = "SELECT * FROM doctors WHERE user_id = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Database error: " . $conn->error); 
    die("<script>alert('Database error. Please try again later.');</script>");
}

// Bind the user_id parameter
$stmt->bind_param("s", $user_id);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the doctor's details as an associative array
    $doctor = $result->fetch_assoc();
} else {
    echo "<script>alert('No doctor details found for this user.');</script>";
    // Exit if no details are found to avoid undefined index errors
    exit;
}


// Query to count today's telemedicine appointments for the logged-in doctor
$sql = "
    SELECT COUNT(*) AS total_appointments
    FROM telemedicine_appointments ta
    JOIN doctors d ON ta.doctor_id = d.doctor_id
    WHERE d.user_id = ? 
    AND ta.appointment_date = CURDATE()
";

// Prepare and bind the query
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_id); // Assuming user_id is a string
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($total_appointments);
    $stmt->fetch();

    // Close the statement
    $stmt->close();
} else {
    die('Error: Failed to prepare the query.');
}





//photo
// Prepare and execute the query to fetch doctor details
$sql = "SELECT doctor_id, user_id, name, specialization, contact_information, email, department, job_position, gender, age, image_url FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Query preparation failed: " . htmlspecialchars($conn->error)); // Log the error for admin
    die("<script>alert('Database error. Please try again later.');</script>");
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($doctor_id, $user_id, $name, $specialization, $contact_information, $email, $department, $job_position, $gender, $age, $image_url);
$stmt->fetch();
$stmt->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SmartMed Fiji | Doctors Dashboard</title>
  <link rel="stylesheet" href="css/doc_dash_styles.css" />
  <!-- Font Awesome Cdn Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js'></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
</head>
<style>
    
     /* General styling for calendar section */
     .main-course {
        max-width: 2000px;
        margin: auto;
        padding: 10px;
        display: flex; gap: 16px; flex-wrap: wrap; justify-content: space-between; align-items: flex-start;
    }

    .course-box {
        background-color: #f4f4f4;
        padding: 20px;
        border-radius: 8px;
        flex:1;
        width: 400px; 
         border: 1px solid #ddd;
          border-radius: 8px; 
          padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    #calendar {
        margin-top: 20px;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    button {
        padding: 5px 10px;
        background-color: #04c9f5 ;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #007bff;
    }

    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 10;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 10px;
        position: relative;
    }

    .close {
        position: absolute;
        right: 10px;
        top: 10px;
        color: #aaa;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    
.dashboard {
    flex-grow: 1;
    padding: 20px;
    background-color: #EDFBFE;
    overflow-y: auto;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #83E5FB;
    padding: 7px;
    border-radius: 10px;
    margin-bottom: 30px;
    margin-top: 20px;
}

header h1 {
    font-size: 36px;
    color: black;
}

header p {
    color: black;
}

.date {
    font-size: 18px;
    color: #666;
}
.doctor-image {
  width: 300px; /* Adjust the width to fit your layout */
  height: auto; /* Keeps the aspect ratio intact */
  border-radius: 50%; /* Makes the image circular */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow */
  margin-left: 20px; /* Adds some spacing from the text */
}


.overview {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 30px;
    margin-top: 30px;
}

.card {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
}
.card i{
    padding-top: 20px;
    margin-bottom: 20px;
}

.recruitment,
.applicants {
    margin-bottom: 30px;
}

.recruitment table {
    width: 100%;
    border-collapse: collapse;
}

.recruitment table th, .recruitment table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.applicants ul {
    list-style: none;
}

.applicants li {
    background-color: #fff;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

</style>
<body>
  <div class="container">
  <nav class="nav-bar">
      <ul>
      <div class="logo">
          <button id="toggle-btn"><i class="fas fa-bars"></i></button>
          <span><img src="../img/logo.jpg" alt=""></span>
        </div>
        <li><a href="doc_dash.php"><i class="fas fa-tachometer-alt"></i><span class="nav-item">Dashboard</span></a></li>
        <li><a href="app/doc_app_index.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Appointments</span></a></li>
        <li><a href="ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">EHR</span></a></li>
        <li><a href="pres/pres_index.html"><i class="fas fa-syringe"></i><span class="nav-item">Add Prescription</span></a></li>
        <li><a href="doc_list/profile.php"><i class="fas fa-chart-line"></i><span class="nav-item">Profile</span></a></li>
        <li><a href="mess/mass_index.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="bill/bill_index.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>


    <!-- Main Dashboard -->
    <main class="dashboard">
 


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
   
            <!-- Header -->
            <header style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
    <div class="welcome" style="flex: 1; max-width: 40%; font-family: Arial, sans-serif;">
    <h1>Welcome.Dr, <?php echo $doctor_name; ?>!</h1>
        <h1 style="font-size: 24px; margin-bottom: 10px;">Our, <?php echo htmlspecialchars($doctor['specialization']); ?></h1>
        <h4 style="margin-bottom: 5px;">Your Doctor ID: <?php echo $doctor_id; ?></h4>
        <p style="margin-bottom: 5px;">Department: <?php echo htmlspecialchars($doctor['department']); ?></p>
        <p style="margin-bottom: 5px;">Contact Information: <?php echo htmlspecialchars($doctor['contact_information'] ?? 'N/A'); ?></p>
        <p>Email: <?php echo htmlspecialchars($doctor['email']); ?></p>
    </div>
    <div class="date" style="flex-shrink: 4;">
        <img src="21.png" alt="doctor" class="doctor-image" style="max-width: 80%; height: auto;">
    </div>
</header>


            <!-- Task Overview Section -->
            <section class="overview">

            <div class="card">
                
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Doctor Image" style="width: 240px; height: 200px; border-radius: 10%; margin-bottom: 15px; object-fit: contain;">
           </div>

                <div class="card">
                <i class="fas fa-calendar-check"></i>
                <h3>Today's Confirmed Appointments:</h3><h3><?php  echo " " . $todays_confirmed_appointments;?>  </h3>
               </div>
                <div class="card">
                    <i class="fas fa-calendar-check"></i><h3> Total Confirmed Appointments :</h3>
                    <h3> <?php  
                        echo " " . $total_confirmed_appointments;
                        ?>           
                   </h3>
                </div>
                <div class="card">
                    <i class="fas fa-procedures"></i><h3> Total Patients:</h3>
                    <h3>
                        <?php     
                        echo " " . htmlspecialchars($total_patient);     ?>
                        </h3>
                </div>
                <div class="card">
                <i class="fas fa-video"></i>
                   <h3>Today's telemedicine appointments:</h3><h3><?php     
                         echo " " . $total_appointments . "";
                         ?></h3>
                </div>
            </section>

            <div class="appointments-container" style="display: flex; justify-content: space-between; gap: 10px;">

            
            <section class="recruitment">
                <?php 
                // Check if the database connection is valid
                if (!$conn) {
                    die('Error: Failed to connect to the database.');
                }
                
                // Assuming you have the user_id of the logged-in user
                $user_id = $_SESSION['user_id']; // Replace with actual session variable for user_id
                
                // Step 1: Use the doctor_id and current date to count today's appointments
                $appointment_sql = "SELECT COUNT(*) AS todays_appointments 
                                    FROM appointments 
                                    WHERE doctor_id = ? AND appointment_date = CURDATE()"; 
                
                $appointment_stmt = $conn->prepare($appointment_sql);
                
                if ($appointment_stmt) {
                    $appointment_stmt->bind_param("i", $doctor_id); 
                    $appointment_stmt->execute();
                    $appointment_stmt->store_result();
                    $appointment_stmt->bind_result($todays_appointments);
                    $appointment_stmt->fetch();
                } else {
                    die('Error: Failed to prepare appointment query.');
                }
                
                // Close the statement
                $appointment_stmt->close();
                
                // Step 2: Fetch appointment details for confirmed appointments
                  $sql = "SELECT a.patient_name, a.appointment_id, a.appointment_date, a.appointment_time, d.name AS doctor_name, a.status, a.reason, a.remark
                        FROM appointments a
                        JOIN doctors d ON a.doctor_id = d.doctor_id
                        WHERE d.user_id = ? AND a.appointment_date = CURDATE() AND a.status = 'Confirmed'";
                
                $details_stmt = $conn->prepare($sql);
                
                if ($details_stmt) {
                    $details_stmt->bind_param("s", $user_id); // Bind the user_id instead of doctor_id
                    $details_stmt->execute();
                    $details_result = $details_stmt->get_result();
                
                    // Check if there are no appointments
                    if ($todays_appointments > 0) {
                        // Display appointments in a table for easy styling and click events
                        echo "<table id='appointmentTable' border='1' style='background-color:white; width:800px;'>
                                <tr>
                                    <th style='background-color:white;'>Patient Name</th>
                                    <th style='background-color:white;'>Appointment Time</th>
                                    <th style='background-color:white;'>Doctor Name</th>
                                    <th style='background-color:white;'>Action</th>
                                </tr>";
                        
                        while ($row = $details_result->fetch_assoc()) {
                            echo "<tr data-appointment='". json_encode($row) ."'>
                                    <td>{$row['patient_name']}</td>
                                    <td>{$row['appointment_time']}</td>
                                    <td>{$row['doctor_name']}</td>
                                    <td><button class='viewDetailsBtn'>View Details</button></td>
                                  </tr>";
                        }
                
                        echo "</table>";
                    } else {
                        // No appointments for today
                        echo "<p>No appointments for today.</p>";
                    }
                
                    $details_stmt->close();
                } else {
                    die('Error: Failed to prepare appointment details query.');
                }
                
                $conn->close();
                ?>
                
                
  <!-- Popup Modal -->
  <div id="appointmentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Appointment Details</h2>
        <p><strong>Patient Name:</strong> <span id="modalPatientName"></span></p>
        <p><strong>Doctor Name:</strong> <span id="modalDoctorName"></span></p>
        <p><strong>Reason:</strong> <span id="modalReason"></span></p>
        <p><strong>Remark:</strong> <span id="modalRemark"></span></p>
    </div>
</div>


            </section>

            <!-- New Applicants -->
            <section class="applicants">
             
<!--Telemedicine remainder-->
<div style="background-color: white; border-radius: 8px; padding: 16px; width: 450px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin: 0 auto;">
    <h3 style="margin-top: 0; font-size: 1.5em; color: #333;">Telemedicine Appointments</h3>
    <?php

include('smf_db_conn.php'); // Ensure your database connection is included

// Assuming you have the user_id of the logged-in user
$user_id = $_SESSION['user_id']; // Replace with actual session variable for user_id

// Query to fetch today's appointments for the logged-in doctor
$query = "
    SELECT 
        a.appointment_id, 
        a.appointment_date, 
        a.appointment_time, 
        a.diagnosis, 
        a.reminder_note, 
        CONCAT(p.firstName, ' ', p.lastName) AS patient_name, 
        a.video_call_link 
    FROM telemedicine_appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN patient p ON a.patient_id = p.patient_id
    WHERE a.appointment_date = CURDATE() 
    AND d.user_id = ?
";

// Prepare and bind the query to prevent SQL injection
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $user_id); // Use 's' if user_id is a string
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<table style="width: 100%; border-collapse: collapse;">';
    echo '<thead><tr><th style="text-align:left; padding: 10px;">Patient Name</th><th style="text-align:left; padding: 10px;">Time</th></tr></thead>';
    echo '<tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; background-color:lightblue;">
                <a href="#" data-toggle="modal" data-target="#appointmentModal' . $row['appointment_id'] . '">
                    ' . htmlspecialchars($row['patient_name']) . '
                </a>
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($row['appointment_time']) . '</td>
        </tr>';

        // Modal for detailed view
        echo '
        <div class="modal fade" id="appointmentModal' . $row['appointment_id'] . '" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel' . $row['appointment_id'] . '" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="appointmentModalLabel' . $row['appointment_id'] . '">Appointment Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Patient Name:</strong> ' . htmlspecialchars($row['patient_name']) . '</p>
                        <p><strong>Date:</strong> ' . htmlspecialchars($row['appointment_date']) . '</p>
                        <p><strong>Time:</strong> ' . htmlspecialchars($row['appointment_time']) . '</p>
                        <p><strong>Diagnosis:</strong> ' . htmlspecialchars($row['diagnosis']) . '</p>
                        <p><strong>Reminder Note:</strong> ' . htmlspecialchars($row['reminder_note']) . '</p>';
                        if (!empty($row['video_call_link'])) {
                            echo '<a href="' . htmlspecialchars($row['video_call_link']) . '" class="btn btn-primary" target="_blank" style="background-color: #007bff; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; display: inline-block;">Join Video Call</a>';
                        }
        echo '
                    </div>
                </div>
            </div>
        </div>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No appointments for today.</p>';
}

// Close the statement and the database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
            </section>
            </div>
        </main>
   </div>
  <script>
document.getElementById('toggle-btn').addEventListener('click', function() {
  const navBar = document.querySelector('.nav-bar');
  const mainContent = document.querySelector('.dashboard');
  
  // Toggle the 'collapsed' class
  navBar.classList.toggle('collapsed');
  mainContent.classList.toggle('collapsed');
});


    // Open the modal and populate it with appointment data
    document.querySelectorAll('.viewDetailsBtn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const appointmentData = JSON.parse(row.getAttribute('data-appointment'));

                // Populate modal with data
                document.getElementById('modalPatientName').innerText = appointmentData.patient_name;
                document.getElementById('modalDoctorName').innerText = appointmentData.doctor_name;
                document.getElementById('modalReason').innerText = appointmentData.reason;
                document.getElementById('modalRemark').innerText = appointmentData.remark;

                // Show the modal
                document.getElementById('appointmentModal').style.display = 'block';
            });
        });

        // Close the modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('appointmentModal').style.display = 'none';
        });
  </script>

</body>
</html>
