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
        <li><a href="patients.php"><i class="fas fa-users"></i><span class="nav-item">Analytics</span></a></li>
        <li><a href="appointments.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Appointments</span></a></li>
        <li><a href="../ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">EHR</span></a></li>
        <li><a href="analytics.php"><i class="fas fa-chart-line"></i><span class="nav-item">Patients</span></a></li>
        <li><a href="mess/mess_index.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="help.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <!-- Main section -->
    <section class="main">
     
    <button onclick="window.location.href='generate_pdf.php'">Download Doctors List as PDF</button>
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


  </script>

</body>
</html>
<?php
// Close connection
$conn->close();
?>