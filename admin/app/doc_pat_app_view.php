<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include('smf_db_conn.php'); // Adjust the path to where your 'smf_db_conn.php' file is located

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Assuming user_id is stored in session
$user_id = $_SESSION['user_id'];

// Prepare and execute the query to fetch the doctor's name
$sql = "SELECT name FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Handle error if query preparation fails
    die('Query preparation failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();



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
        <li><a href="settings.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="help.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <!-- Main section -->
    <section class="main">
      <div class="main-top" style="display: flex; align-items: center; justify-content: space-between;">
        <h1>Appointment Overview</h1>
        <div class="dropdown-wrapper" style="display: flex; align-items: center; gap: 50px;">
          
          <!-- Notification dropdown (optional if appointments are uncommented) -->
          <div class="custom-dropdown-menu">
            <a href="#" class="custom-dropdown-toggle"><i class="fas fa-bell"></i></a>
            <div class="custom-dropdown-content">
              <?php if (!empty($appointments)): ?>
                <?php foreach ($appointments as $row): ?>
                  <a href="view-appointment-detail.php?editid=<?php echo $row->ID; ?>&&aptid=<?php echo $row->AppointmentNumber; ?>" class="custom-dropdown-item">
                    <div class="custom-media">
                      <div class="custom-media-left">
                        <div class="custom-avatar">
                          <img src="assets/images/images.png" alt="Avatar">
                          <i class="custom-status"></i>
                        </div>
                      </div>
                      <div class="custom-media-body">
                        <h5 class="custom-media-heading">New Appointment</h5>
                        <small class="custom-media-meta"><?php echo $row->AppointmentNumber; ?> at (<?php echo $row->ApplyDate; ?>)</small>
                      </div>
                    </div>
                  </a>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="custom-dropdown-item text-center" style="color: gray;">No appointments available</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Profile dropdown -->
          <div class="dropdown">
            <button class="dropbtn"><i class="fas fa-user-cog"></i></button>
            <div class="dropdown-content">
              <a href="inc/update_doc.html">Profile</a>
              <a href="settings.php">Settings</a>
              <a href="logout.php">Log out</a>
            </div>
          </div>
        </div>
      </div>

      <div class="header">
</div>
<div class="main-content" id="main-content">
       

        <!-- Patient Info Section -->
        <section class="patient-info">
            <div class="patient-card">
                <img src="profile.jpg" alt="Patient">
                <h3>Ludmila Sidoroshina</h3>
                <p>ludmilas@example.com</p>
                <button class="message-btn">Send Message</button>
                <div class="appointments">
                    <p>5 Past</p>
                    <p>2 Upcoming</p>
                </div>
            </div>
            <div class="notes-section">
                <h4>Notes</h4>
                <p>1. The patient needs a full checkup.</p>
                <p>2. Treatment for hypertension and kidney issues.</p>
                <button class="save-btn">Save Note</button>
            </div>
        </section>

        <!-- Files/Payments Section -->
        <section class="files-payments">
            <div class="files">
                <h4>Files/Documents</h4>
                <ul>
                    <li><a href="#">Blood test.pdf</a> <span>27.5kB</span></li>
                    <li><a href="#">Prescription.pdf</a> <span>29.3kB</span></li>
                    <li><a href="#">X-ray results.pdf</a> <span>33kB</span></li>
                </ul>
                <button class="add-file-btn">Add File</button>
            </div>
            <div class="payments">
                <h4>Payments</h4>
                <ul>
                    <li>Consultation with Doctor <span>$25</span></li>
                    <li>Medication <span>$10</span></li>
                </ul>
            </div>
        </section>

        <!-- Appointments Section -->
        <section class="appointments">
            <h4>Appointments</h4>
            <ul>
                <li>01 Jun '20 - Consultation with Dr. Advasty Ch. <span>Completed</span></li>
                <li>04 Jun '20 - Treatment with Dr. Advasty Ch. <span>Upcoming</span></li>
            </ul>
            <button class="add-appointment-btn">Add Appointment</button>
        </section>
    </div>


</div>

       
      </section>
    </section>
  </div>

  <!-- JavaScript for toggle button -->
  <script>
    const toggleBtn = document.getElementById('toggle-btn');
    const navBar = document.querySelector('.nav-bar');
    toggleBtn.addEventListener('click', () => {
      navBar.classList.toggle('collapsed');
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

  



  </script>

</body>
</html>