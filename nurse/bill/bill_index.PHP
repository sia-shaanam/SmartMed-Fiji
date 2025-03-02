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




// Fetch all existing appointments to populate the dropdown
$sql = "SELECT appointment_id, patient_name, appointment_date FROM appointments";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
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
     .con{
            width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #04c9f5;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .bill-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
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
        <li><a href="../mess/mass_index.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="bill_index.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <section class="main">
    <div class="main-top" style="display: flex; align-items: center; justify-content: space-between;">
    <h1 style="text-align: center; font-size:36px; margin-bottom:20px;">Bill Receipt Generator</h1>   
     
    </div>



    <div class="con">
        <h1>Billing System - Smart Med Fiji</h1>
        
        <!-- Form for selecting appointment -->
        <form action="billing.php" method="POST">
            <label for="appointment_id">Select Appointment:</label>
            <select name="appointment_id" id="appointment_id" required>
                <option value="">--Select Appointment--</option>
                <?php
                if ($result->num_rows > 0) {
                    // Loop through all appointments and create options in the dropdown
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['appointment_id'] . "'>" 
                             . $row['appointment_id'] . " - " . $row['patient_name'] . " (Date: " . $row['appointment_date'] . ")"
                             . "</option>";
                    }
                } else {
                    echo "<option value=''>No appointments available</option>";
                }
                ?>
            </select>
            <button type="submit">Retrieve Bill</button>
        </form>

        <div id="bill-details" class="bill-details">
            <?php if (isset($_GET['appointment_id'])): ?>
                <?php include 'billing.php'; ?>
            <?php endif; ?>
        </div>
    </div>
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

</body>
</html>
<?php
$conn->close();
?>
