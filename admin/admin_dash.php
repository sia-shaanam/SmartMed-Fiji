<?php
session_start();
include('smf_db_conn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not, redirect to login page
    header('Location: login.php');
    exit();
}

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Assuming admin_id is stored in session
$user_id = $_SESSION['user_id'];

// Prepare and execute the query to fetch the admin's name
$sql = "SELECT name FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Handle error: prepare failed
    die('Query preparation failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $admin_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();



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



// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SmartMed Fiji | Admin Dashboard</title>
  <link rel="stylesheet" href="css/doc_dash_styles.css" />
  <!-- Font Awesome Cdn Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
/* Parent grid container for the charts */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 3 equal columns */
    gap: 20px; /* Space between grid items */
    padding: 20px;
    width: 100%; /* Full width of the section */
    box-sizing: border-box; /* Include padding in width calculation */
}

/* Individual chart + title container */
.chart-item {
    display: flex;
    flex-direction: column; /* Stack title and chart vertically */
    align-items: center; /* Center-align content */
    text-align: center;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

/* Chart title */
.chart-item h2 {
    margin-bottom: 15px;
    font-size: 1.5rem;
    color: #333;
}

/* Chart container to ensure proper spacing */
.chart-container {
    width: 100%; /* Make chart container full width */
}

/* Ensure canvas adjusts within the container */
canvas {
    width: 100%;
    height: 300px; /* Set consistent height for charts */
    max-height: 400px; /* Optional: Limit max height */
}

/* Responsive design for smaller screens */
@media (max-width: 768px) {
    .charts-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 columns on tablets */
    }
}

@media (max-width: 480px) {
    .charts-grid {
        grid-template-columns: 1fr; /* 1 column on small screens */
    }
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
        <li><a href="admin_dash.php"><i class="fas fa-tachometer-alt"></i><span class="nav-item">Home</span></a></li>
        <li><a href="doc_list/doc_list.php"><i class="fas fa-users"></i><span class="nav-item">User Management</span></a></li>
        <li><a href="ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">Patients</span></a></li>
        <li><a href="app/doc_app_index.php"><i class="fas fa-file-medical"></i><span class="nav-item">Appointments</span></a></li>
       <li><a href="backup/index.php"><i class="fas fa-chart-line"></i><span class="nav-item">Backup & Recovery</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <section class="main">
    <div class="main-top" style="display: flex; align-items: center; justify-content: space-between;">
        <h1 style="margin: 0;">Welcome, Admin <?php echo htmlspecialchars($name); ?>!</h1>
        <div class="dropdown-wrapper" style="display: flex; align-items: center; gap: 50px;">
           
        </div>
        
    </div>
   
    <div class="main-skills">
    <div class="card"><i class="fas fa-calendar-check"></i><h3>
        
         <?php
         include 'smf_db_conn.php';
         
         // SQL query to count today's appointments across all doctors
         $appointment_sql = "SELECT COUNT(*) AS todays_appointments 
                             FROM appointments 
                             WHERE appointment_date = CURDATE()"; // CURDATE() gives today's date
         
         $appointment_stmt = $conn->prepare($appointment_sql);
         
         if ($appointment_stmt) {
             $appointment_stmt->execute();
             $appointment_stmt->bind_result($todays_appointments); // Bind result to todays_appointments
             $appointment_stmt->fetch(); // Fetch the result
             $appointment_stmt->close(); // Close the appointment statement
         
             echo "Today's Appointments : $todays_appointments";
         } else {
             die('Error: Failed to prepare appointment query.');
         }
         ?>
                </h3></div>


                <div class="card"><i class="fas fa-calendar-check"></i><h3> 
             <?php  
             include('smf_db_conn.php');
                $total_appointment_sql = "SELECT COUNT(*) AS total_appointments FROM appointments";

                $total_appointment_stmt = $conn->prepare($total_appointment_sql);
                
                if ($total_appointment_stmt) {
                    $total_appointment_stmt->execute();
                    $total_appointment_stmt->bind_result($total_appointments); // Bind result to $total_appointments
                    $total_appointment_stmt->fetch(); // Fetch the result
                    $total_appointment_stmt->close(); // Close the statement
                    echo "Total Appointments: $total_appointments";
                } else {
                    die('Error: Failed to prepare total appointments query.');
                }
                     
                 ?>           
               </h3>
            </div>


            <div class="card">
                <i class="fa fa-video"></i>
                   <h3>
                   <?php
include 'smf_db_conn.php';

// SQL query to count today's telemedicine appointments across all doctors
$sql = "
    SELECT COUNT(*) AS total_appointments
    FROM telemedicine_appointments ta
    WHERE ta.appointment_date = CURDATE()
";

// Prepare and bind the query
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $stmt->store_result(); // Store result for count
    $stmt->bind_result($total_appointments);
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    echo "Today's Telemedicine Appointments : $total_appointments";
} else {
    die('Error: Failed to prepare the query.');
}
?>

                   </h3>
                </div>
       
                <div class="card">
                <i class="fa fa-video"></i>
                    <h3>
                    <?php
include 'smf_db_conn.php';

// SQL query to count total telemedicine appointments across all doctors
$sql_total = "
    SELECT COUNT(*) AS total_appointments
    FROM telemedicine_appointments
";

// Prepare and bind the query
$stmt_total = $conn->prepare($sql_total);
if ($stmt_total) {
    $stmt_total->execute();
    $stmt_total->store_result(); // Store result for count
    $stmt_total->bind_result($total_appointments);
    $stmt_total->fetch();

    // Close the statement
    $stmt_total->close();

    echo "Total Telemedicine Appointments: $total_appointments";
} else {
    die('Error: Failed to prepare the total appointments query.');
}
?>

                        </h3>
                </div>



               

                <div class="card"><i class="fas fa-notes-medical"></i><h3>Total Patients: 
</h3><h3>   <?php     
             echo "" . htmlspecialchars($total_patient);     ?></h3></div>
        
        
      
       

        <div class="card"><i class="fas fa-notes-medical"></i><h3>
        <?php
include 'smf_db_conn.php';

// SQL query to count total male and female patients
$total_patients_sql = "
    SELECT 
        SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) AS total_males,
        SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) AS total_females
    FROM patient
";

$total_patient_stmt = $conn->prepare($total_patients_sql);

if ($total_patient_stmt) {
    $total_patient_stmt->execute();
    $total_patient_stmt->bind_result($total_males, $total_females); // Bind result to total_males and total_females
    $total_patient_stmt->fetch(); // Fetch the result
    $total_patient_stmt->close(); // Close the statement

    // Output the total counts
    echo "Total Male Patients: $total_males<br>";
    echo "Total Female Patients: $total_females<br>";
} else {
    die('Error: Failed to prepare total patient query.');
}
?>

        </h3></div>
        
            


    </div>
    <section class="main-course">
    <div class="charts-grid">
        <!-- Appointment Chart -->
        <div class="chart-item">
            <h2>Appointments</h2>
            <div class="chart-container">
                <canvas id="appointmentChart"></canvas>
            </div>
        </div>

        <!-- Doctor Chart -->
        <div class="chart-item">
            <h2>Doctors As Per Appointment</h2>
            <div class="chart-container">
                <canvas id="doctorChart"></canvas>
            </div>
        </div>

        <!-- Message Chart -->
        <div class="chart-item">
            <h2>Messages</h2>
            <div class="chart-container">
                <canvas id="messageChart"></canvas>
            </div>
        </div>
    </div>
</section>



    </section>
  </div>

  <script>
    const toggleBtn = document.getElementById('toggle-btn');
    const navBar = document.querySelector('.nav-bar');
    toggleBtn.addEventListener('click', () => {
      navBar.classList.toggle('collapsed');
    });



     // Appointment Chart
     fetch('fetch_appointments.php')
            .then(response => response.json())
            .then(data => {
                const statuses = data.map(item => item.status);
                const totals = data.map(item => item.total);

                new Chart(document.getElementById('appointmentChart').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: statuses,
                        datasets: [{
                            label: 'Appointment Status',
                            data: totals,
                            backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
                        }]
                    }
                });
            });

        // Doctor Chart
        fetch('fetch_doctors.php')
            .then(response => response.json())
            .then(data => {
                const doctorNames = data.map(item => item.name);
                const totalAppointments = data.map(item => item.total_appointments);

                new Chart(document.getElementById('doctorChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: doctorNames,
                        datasets: [{
                            label: 'Appointments per Doctor',
                            data: totalAppointments,
                            backgroundColor: '#2196F3'
                        }]
                    }
                });
            });

        // Message Chart
        fetch('fetch_messages.php')
            .then(response => response.json())
            .then(data => {
                const dates = data.map(item => item.date);
                const messageCounts = data.map(item => item.message_count);

                new Chart(document.getElementById('messageChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Messages Over Time',
                            data: messageCounts,
                            borderColor: '#673AB7',
                            fill: false
                        }]
                    }
                });
            });
  </script>

</body>
</html>
