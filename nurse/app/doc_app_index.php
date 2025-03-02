<?php
// Start the session
session_start();

// Check if the user is logged in and has the role 'doctor'
if (!isset($_SESSION['user_id']) ) {
    header("Location: ../login.php"); // Redirect to login if not authenticated
    exit();
}

// Include the database connection file
include('smf_db_conn.php');

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];




 // Step 2: Use the doctor_id and current date to count today's appointments
 $appointment_sql = "SELECT COUNT(*) AS todays_appointments 
 FROM appointments 
 WHERE doctor_id = ? AND appointment_date = CURDATE()"; // CURDATE() gives today's date
$appointment_stmt = $conn->prepare($appointment_sql);

if ($appointment_stmt) {
$appointment_stmt->bind_param("i", $doctor_id);
$appointment_stmt->execute();
$appointment_stmt->store_result();
$appointment_stmt->bind_result($todays_appointments);
$appointment_stmt->fetch();


} else {
die('Error: Failed to prepare appointment query.');
}// Close the prepared statements
$appointment_stmt->close();




$sql = "SELECT a.patient_name, a.appointment_id, a.appointment_date, a.appointment_time, d.name as doctor_name, a.status, a.reason, a.remark
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE 1=1";



?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SmartMed Fiji | Doctors Dashboard</title>
  <link rel="stylesheet" href="../css/doc_dash_styles.css" />
  <link rel="stylesheet" href="styles.css">
  <!-- Font Awesome CDN Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js'></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
</head>
<style>
  
/* Main Content Styling */
main {
  flex: 1;
  padding: 20px;
  background-color: #ffffff;
  border-radius: 10px;
  margin: 20px;
}

header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.search-bar input {
  padding: 10px;
  border-radius: 20px;
  border: 1px solid #e0e0e0;
  width: 300px;
}

.user-actions {
  display: flex;
  align-items: center;
}

.user-actions button {
  background:  #04c9f5;
  border: none;
  margin-left: 15px;
  cursor: pointer;
}

.profile {
  display: flex;
  align-items: center;
}

.profile img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-right: 10px;
}

/* Product Section */
.product-section {
  background-color:  #003366;
  padding: 20px;
  border-radius: 10px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  align-items: center;
}

.filters span {
  margin-right: 10px;
}

.filters select, 
.filters button {
  margin-right: 10px;
  padding: 10px;
  border-radius: 5px;
  border: none;
  background-color:   #04c9f5;
  color: white;
}

.add-product {
  background-color: #04c9f5;
  color: white;
}

.product-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid #e0e0e0;
}

td img {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}

.status {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 12px;
}

.status.pending {
  background-color: #ffeb3b;
  color: #856404;
}

.status.active {
  background-color: #4caf50;
  color: #fff;
}

.status.inactive {
  background-color: #f44336;
  color: #fff;
}

.status.bouncing {
  background-color: #ff9800;
  color: #fff;
}

.status.on-sale {
  background-color: #2196f3;
  color: #fff;
}

button {
  background: none;
  border: none;
  cursor: pointer;
}


  /* Patient Info Section */
  .patient-info {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        margin-left: 20px;
    }

    .patient-card {
        background-color: white;
        padding: 10px;
        border-radius: 10px;
        width: 15%;
        text-align: center;
    }

    .patient-card img {
        width: 20px;
        height: 80px;
        border-radius: 50%;
    }

    .patient-card h3 {
        margin: 10px 0;
    }

    .patient-card .message-btn {
        padding: 10px 20px;
        background-color: #003366;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .notes-section {
        background-color:  #fff;
        padding: 10px;
        border-radius: 10px;
        width: 90%;
    }

    


          #calendar {
            max-width: 1200px; /* Smaller calendar width */
            margin: 0 auto;
        }

        /* Style for the popup (modal) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px; /* Max width of popup */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
</style>
<body>
  <div class="container">
    <!-- Navigation bar -->
    <nav class="nav-bar">
      <ul>
        <div class="logo">
          <button id="toggle-btn"><i class="fas fa-bars"></i></button>
          <span><img src="../img/logo.jpg" alt=""></span>
        </div>
        <li><a href="../doc_dash.php"><i class="fas fa-tachometer-alt"></i><span class="nav-item">Dashboard</span></a></li>
        <li><a href="doc_app_index.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Appointments</span></a></li>
        <li><a href="../ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">EHR</span></a></li>
        <li><a href="../mess/mass_index.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="../bill/bill_index.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
       </ul>
    </nav>

    <!-- Main section -->
    <section class="main">
      <div class="main-top" style="display: flex; align-items: center; justify-content: space-between;">
        <h1>Appointment Overview</h1>
        <div class="dropdown-wrapper" style="display: flex; align-items: center; gap: 50px;">
          
        
  
        </div>
      </div>

  <!-- Patient Info Section -->
  <section class="patient-info">
  <div class="patient-card" style="background-color:  #04c9f5; color:white;">
    

    <div class="appointments">
        <a href="confirm-appointments.php">
        <button class="message-btn" style="color:white; background-color:  #04c9f5;">Manage Appointments</button>
    </a>
    </div>
</div>

<div>


</div>






        </section>
        <main>


        

        <section class="appointment-section">
    <div class="section-header">
        <h2>Appointments</h2>
        <div class="filters">
            <form method="GET" action="">
                <span>Showing</span>
                <select name="limit" onchange="this.form.submit()">
                    <option value="10" <?php if (isset($_GET['limit']) && $_GET['limit'] == '10') echo 'selected'; ?>>10</option>
                    <option value="20" <?php if (isset($_GET['limit']) && $_GET['limit'] == '20') echo 'selected'; ?>>20</option>
                    <option value="50" <?php if (isset($_GET['limit']) && $_GET['limit'] == '50') echo 'selected'; ?>>50</option>
                </select>

                <!-- Filter by Doctor -->
                <label for="doctor_id">Doctor:</label>
                <select name="doctor_id" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php
                    // Fetch list of doctors to populate the dropdown
                    $doctor_sql = "SELECT doctor_id, name FROM doctors";
                    $doctor_result = $conn->query($doctor_sql);
                    if ($doctor_result->num_rows > 0) {
                        while ($doctor = $doctor_result->fetch_assoc()) {
                            echo "<option value='" . $doctor['doctor_id'] . "' " . (isset($_GET['doctor_id']) && $_GET['doctor_id'] == $doctor['doctor_id'] ? 'selected' : '') . ">" . $doctor['name'] . "</option>";
                        }
                    }
                    ?>
                </select>

                <!-- Filter by Status -->
                <label for="status">Status:</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="Pending" <?php if (isset($_GET['status']) && $_GET['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Confirmed" <?php if (isset($_GET['status']) && $_GET['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                  <!--  <option value="Cancelled" <?php if (isset($_GET['status']) && $_GET['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>-->
                </select>
                <a href="add-appointment.php">
                <button type="button" class="add-appointment" style="color:white; background-color:  #04c9f5;">+ Add New Appointment</button></a>
            </form>
        </div>
    </div>
    <div class="appointment-table">
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Patient ID</th> <!-- Changed from Patient Name to Patient ID -->
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Remark</th>
          </tr>
        </thead>
        <tbody>
            <?php
            // Handle filters and limit
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';
            $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';

            // SQL query to fetch appointments with filters
            $sql = "SELECT a.patient_id, a.patient_name, a.appointment_id, a.appointment_date, a.appointment_time, d.name as doctor_name, a.status, a.reason, a.remark
                    FROM appointments a
                    JOIN doctors d ON a.doctor_id = d.doctor_id
                    WHERE 1=1";

            // Apply doctor filter
            if (!empty($doctor_id)) {
                $sql .= " AND a.doctor_id = '$doctor_id'";
            }

            // Apply status filter
            if (!empty($status)) {
                $sql .= " AND a.status = '$status'";
            }

            // Apply limit
            $sql .= " LIMIT $limit";

            // Execute the query
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['appointment_id'] . "</td>";
                    echo "<td>" . $row['patient_id'] . "</td>"; // Display patient_id
                    echo "<td>" . $row['patient_name'] . "</td>";
                    echo "<td>" . $row['appointment_date'] . "</td>";
                    echo "<td>" . $row['appointment_time'] . "</td>";
                    echo "<td>" . $row['doctor_name'] . "</td>";

                    // Status badge logic
                    echo "<td><span class='status " . strtolower($row['status']) . "'>" . $row['status'] . "</span></td>";

                    // Handle reason and remark keys carefully
                    echo "<td>" . (isset($row['reason']) ? $row['reason'] : 'N/A') . "</td>";
                    echo "<td>" . (isset($row['remark']) ? $row['remark'] : 'N/A') . "</td>";

                
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No appointments found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>




</section>


</main>



        </section>
    </div>


</div>

       
      </section>
    </section>
  </div>

  <!-- JavaScript for toggle button -->
  <script>
    document.getElementById('toggle-btn').addEventListener('click', function() {
  const navBar = document.querySelector('.nav-bar');
  const mainContent = document.querySelector('.main');
  
  // Toggle the 'collapsed' class
  navBar.classList.toggle('collapsed');
  mainContent.classList.toggle('collapsed');
});

    function toggleSearchResults(show) {
            const searchResults = document.getElementById('search-results');
            const allPatients = document.getElementById('all-patients');
            if (show) {
                searchResults.style.display = 'block';
                allPatients.style.display = 'none';
            } else {
                searchResults.style.display = 'none';
                allPatients.style.display = 'block';
            }
        }

        // Automatically show results if search was performed
        window.onload = function() {
            <?php if (!empty($search_results)) { ?>
                toggleSearchResults(true);
            <?php } else { ?>
                toggleSearchResults(false);
            <?php } ?>
        };

  


    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridWeek', // Set initial view to week for more compactness
        height: 'auto', // Automatically adjust the calendar height
        events: 'fetch-confirmed-appointments.php', // Fetch confirmed appointments
        headerToolbar: { // Simplified toolbar
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridDay,dayGridWeek'
        },
        eventClick: function(info) {
            // Show the modal with the event details
            var modal = document.getElementById("appointmentModal");
            var span = document.getElementsByClassName("close")[0];

            // Populate the modal with event data
            document.getElementById('patientName').textContent = info.event.title;
            document.getElementById('doctorId').textContent = info.event.extendedProps.doctor_id;
            document.getElementById('reason').textContent = info.event.extendedProps.reason;
            document.getElementById('remark').textContent = info.event.extendedProps.remark;

            // Display the modal
            modal.style.display = "block";

            // Close the modal when clicking on the 'x'
            span.onclick = function() {
                modal.style.display = "none";
            }

            // Close the modal when clicking outside the modal content
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    });

    calendar.render();
});
  </script>

</body>
</html>
<?php
// Close connection
$conn->close();
?>