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

// Assuming user_id is stored in session
$user_id = $_SESSION['user_id'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SmartMed Fiji | Doctors Dashboard</title>
  <link rel="stylesheet" href="../css/doc_dash_styles.css" />
  <!-- Font Awesome Cdn Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
       

        .con {
            max-width: 800px;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: black;
        }

        .message-cards {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 20px;
        }

        .message-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-card .info {
            display: flex;
            flex-direction: column;
        }

        .message-card .info h4 {
            margin: 0;
            color: #04c9f5;
            font-size: 18px;
        }

        .message-card .info p {
            margin: 5px 0;
            color: #888;
        }

        .message-card .info span {
            font-size: 14px;
            color: #007bff;
        }

        .message-card .actions {
            display: flex;
            gap: 10px;
        }

        .message-card .actions button {
            background-color: #04c9f5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .message-card .actions button.chat {
            background-color:#04c9f5;
        }

        .message-card .actions button.call {
            background-color: #007bff;
        }

        .message-card .actions button:hover {
            background-color: #0056b3;
        }
        .video-call-button {
  display: inline-flex;  
  align-items: center;
  gap: 10px;           
  padding: 12px 24px;  
  background-color: #04c9f5;
  color: white;        
  border-radius: 5px;   
  text-decoration: none; 
  font-size: 16px;     
  font-weight: 500;    
  transition: background-color 0.3s ease, transform 0.2s ease; 
}

.video-call-button i {
  font-size: 20px; 
}

.video-call-button:hover {
  background-color: #0056b3; 
  transform: translateY(-2px); 
}

.video-call-button:active {
  background-color: #004085; 
  transform: translateY(0); 
}

.video-call-button .nav-item {
  font-size: 16px; 
}

.contain {
    display: flex;
    justify-content: space-between;
    gap: 20px; /* Space between the two divs */
}


.tele {
    flex: 1; /* Make both divs take equal width */
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Customize the look of the buttons and cards */
.message-card, .tele {
    margin-bottom: 20px;
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
        <li><a href="../doc_dash.php"><i class="fas fa-tachometer-alt"></i><span class="nav-item">Dashboard</span></a></li>
        <li><a href="../app/doc_app_index.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Appointments</span></a></li>
        <li><a href="../ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">EHR</span></a></li>
        <li><a href="mass_index.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="../bill/bill_index.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
       </ul>
    </nav>

    <section class="main">
    <div class="main-top">
    <h1 style="text-align: center; font-size:36px; margin-bottom:20px;">Telemedicine</h1>

    </div>

    


<div class="contain">
   
    <div class="tele">
  
  <a href="#" id="video-call-btn" class="video-call-button" style="margin-bottom:20px;">
    <i class="fas fa-video"></i><span class="nav-item"></span>
  </a>



  <a href="../telemed/tele_index.html" style="display: inline-block;margin-left:20px; padding: 8px 10px; background-color: #04c9f5; color: white; text-decoration: none; border-radius: 4px; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#17a2b8'" onmouseout="this.style.backgroundColor='#20c997'">
    <span class="nav-item">Add Your Tele Appointment</span>
</a>

<h2>Upcoming Telemedicine Appointments</h2>
        
   
        
<!--Telemedicine remainder-->
<div style="background-color: white; border-radius: 8px; padding: 16px; max-width: 600px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin: 0 auto;">
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
            <td style="padding: 10px; border-bottom: 1px solid #ddd; background-color:#04c9f5;">
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

</div>







    </div>
</div>

<h2  style="padding:20px;"> Staff's Upcoming Telemedicine Calls</h2>
<?php
// Connect to the database
$connection = new mysqli("localhost", "root", "", "smf_db");

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// SQL query
$sql = "SELECT 
            telemedicine_id,
            appointment_id,
            video_call_link,
            doctor_name,
            appointment_time,
            appointment_date,
            status
        FROM telemedicine_appointments
        WHERE appointment_date >= CURDATE()";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Define the table with inline styling
    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; text-align: left; background-color:white;'>
            <tr style='background-color:  #add3e6;'>
                <th style='padding: 8px;'>Video Call Link</th>
                <th style='padding: 8px;'>Doctor Name</th>
                <th style='padding: 8px;'>Appointment Time</th>
                <th style='padding: 8px;'>Appointment Date</th>
                <th style='padding: 8px;'>Status</th>
                <th style='padding: 8px;'>Edit Status</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td style='padding: 8px;'>
    <a href='" . $row["video_call_link"] . "' target='_blank' style='display: inline-block; padding: 8px 10px; background-color:#20c997; color: white; text-decoration: none; border-radius: 4px; transition: background-color 0.3s;'>
        Join Call
    </a>
</td>

                <td style='padding: 8px;'>" . $row["doctor_name"] . "</td>
                <td style='padding: 8px;'>" . $row["appointment_time"] . "</td>
                <td style='padding: 8px;'>" . $row["appointment_date"] . "</td>
                <td style='padding: 8px;'>" . $row["status"] . "</td>
                <td style='padding: 8px;'>
                    <form method='POST' action='edit_status.php'>
                        <input type='hidden' name='telemedicine_id' value='" . $row["telemedicine_id"] . "'>
                        <select name='new_status' style='padding: 4px;'>
                            <option value='Pending' " . ($row["status"] == "Pending" ? "selected" : "") . ">Pending</option>
                            <option value='Confirmed' " . ($row["status"] == "Confirmed" ? "selected" : "") . ">Confirmed</option>
                            <option value='Completed' " . ($row["status"] == "Completed" ? "selected" : "") . ">Completed</option>
                            <option value='Cancelled' " . ($row["status"] == "Cancelled" ? "selected" : "") . ">Cancelled</option>
                        </select>
                        <input type='submit' value='Update' style='padding: 4px 8px; margin-top: 4px;'>
                    </form>
                </td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No upcoming appointments.";
}

$connection->close();
?>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>


    

    
    </section>
  </div>

  <script>
   document.getElementById('toggle-btn').addEventListener('click', function() {
  const navBar = document.querySelector('.nav-bar');
  const mainContent = document.querySelector('.main');
  
  // Toggle the 'collapsed' class
  navBar.classList.toggle('collapsed');
  mainContent.classList.toggle('collapsed');
});
  </script>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
 document.getElementById('video-call-btn').addEventListener('click', function () {
    const domain = "meet.jit.si";
    const roomName = "SmartMedTelemedicineRoom_<?php echo htmlspecialchars($doctor_name); ?>";
    const url = `https://${domain}/${roomName}`;

    // Open the telemedicine session in a new browser tab
    const newWindow = window.open(url, '_blank', 'width=800,height=600');

    if (newWindow) {
        newWindow.focus(); // Bring the new window to the front
    } else {
        alert('Please allow popups for this website to open the video call.');
    }
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
