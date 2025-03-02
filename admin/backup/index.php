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

$conn->close();
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
   h1 {
            text-align: center;
            color: #2c3e50;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #2980b9;
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 24px;
            }

            form {
                padding: 15px;
            }

            button {
                font-size: 14px;
            }
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
        <li><a href="../admin_dash.php"><i class="fas fa-tachometer-alt"></i><span class="nav-item">Home</span></a></li>
        <li><a href="../doc_list/doc_list.php"><i class="fas fa-users"></i><span class="nav-item">User Management</span></a></li>
        <li><a href="../ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">Patients</span></a></li>
        <li><a href="../app/doc_app_index.php"><i class="fas fa-file-medical"></i><span class="nav-item">Appointments</span></a></li>
       <li><a href="index.php"><i class="fas fa-chart-line"></i><span class="nav-item">Backup & Recovery</span></a></li>
        <li><a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <!-- Main section -->
    <section class="main">
    <a href="../reports/generate_pdf.php"  style="
        display: inline-block; 
        padding: 10px 10px; 
        background-color: #003366;
        color: white; 
        text-align: center; 
        text-decoration: none; 
        border: none; 
        border-radius: 5px; 
        font-size: 16px; 
        margin-bottom:20px;
        margin-top:10px;
        transition: background-color 0.3s ease;"
        onmouseover="this.style.backgroundColor='#45a049';" 
        onmouseout="this.style.backgroundColor='#003366';">Generate Doctors List</a>
     
      
    <h1>Retrieve Patient Records</h1>
    <form action="retrieve.php" method="post">
        <label for="patient_id">Patient ID:</label>
        <input type="text" id="patient_id" name="patient_id" required>
        <button type="submit">Retrieve Records</button>
    </form> 
      
    <h1>Retrieve Doctor Records</h1>
    <form action="pdf.php" method="post"> <!-- Update the action to point to your PHP script -->
        <label for="doctor_id">Doctor ID:</label>
        <input type="text" id="doctor_id" name="doctor_id" required>
        <button type="submit">Retrieve Records</button>
        </form>

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

  



  </script>

</body>
</html>
