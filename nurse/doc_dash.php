<?php
// Start the session securely
session_start();

// Check if the user is logged in and has the role 'nurse'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Include the database connection file
include('smf_db_conn.php');

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Prepare and execute the query to fetch the nurse's details
$stmt = $conn->prepare("SELECT name, nurse_id FROM nurses WHERE nurse_id = ?");
if ($stmt === false) {
    error_log("Database error: " . $conn->error); // Log the error for admin
    die("<script>alert('Database error. Please try again later.');</script>");
}

// Use nurse_id to find the nurse details
$stmt->bind_param("s", $user_id); // Assuming user_id is the same as nurse_id in the session
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Fetch the nurse's details
    $nurse = $result->fetch_assoc();
    $nurse_name = $nurse['name'];
    $nurse_id = $nurse['nurse_id'];
   
    // You can now use these variables as needed in your application
} else {
    // Handle case where no nurse was found
    echo "<script>alert('No nurse found with this user ID. Please contact support.');</script>";
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
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
/* Dashboard main content styling */
.dashboard {
    
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ffffff;
  }

  /* Header styling */
  .header {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 260%;
    background-color: #e8eaf6;
    color: #283593;
    transition: all 0.3s ease;
  }

  /* Welcome message styling */
  .header h1 {
    font-size: 2em;
    margin: 0;
    font-weight: bold;
    color: #3949ab;
  }

  /* Image styling */
  .header img {
    width: 500px;
    height: auto;
    border-radius: 50%;
    margin-top: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .header {
      width: 90%;
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
        <li><a href="doc_dash.php"><i class="fas fa-tachometer-alt"></i><span class="nav-item">Dashboard</span></a></li>
        <li><a href="app/doc_app_index.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Appointments</span></a></li>
        <li><a href="ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">EHR</span></a></li>
        <li><a href="mess/mass_index.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="bill/bill_index.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>


    <!-- Main Dashboard -->
    <main class="dashboard">
 
<main class="dashboard">
      <!-- Header with welcome message and nurse photo -->
      <div class="header">
        <h1>Welcome, Nurses!</h1>
        <img src="21.png" alt="Nurse Image">
      </div>
    </main>

           

           
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


  </script>

</body>
</html>
