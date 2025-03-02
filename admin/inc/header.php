<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SmartMed Fiji | Doctors Dashboard</title>
  <link rel="stylesheet" href="css/doc_dash_styles.css" />
  <!-- Font Awesome Cdn Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <nav>
  <ul>
    <!-- Logo and brand name -->
    <li><a href="#" class="logo">
      <img src="../img/logo.jpg" alt="SmartMed Fiji Logo">
      <span class="nav-item">SmartMed Fiji</span>
    </a></li>

    <!-- Dashboard -->
    <li><a href="dashboard.php">
      <i class="fas fa-home"></i>
      <span class="nav-item">Dashboard</span>
    </a></li>

    <!-- Patients Management -->
    <li><a href="patients.php">
      <i class="fas fa-user"></i>
      <span class="nav-item">Patients</span>
    </a></li>

    <!-- Appointments Management -->
    <li><a href="appointments.php">
      <i class="fas fa-calendar-alt"></i>
      <span class="nav-item">Appointments</span>
    </a></li>

    <!-- Medical Tasks and To-Dos -->
    <li><a href="tasks.php">
      <i class="fas fa-tasks"></i>
      <span class="nav-item">Tasks</span>
    </a></li>

    <!-- Analytics and Reports (e.g., patient trends, treatment outcomes) -->
    <li><a href="analytics.php">
      <i class="fas fa-chart-bar"></i>
      <span class="nav-item">Analytics</span>
    </a></li>

    <!-- System Settings -->
    <li><a href="settings.php">
      <i class="fas fa-cog"></i>
      <span class="nav-item">Settings</span>
    </a></li>

    <!-- Help and Support (for FAQs, contact information) -->
    <li><a href="help.php">
      <i class="fas fa-question-circle"></i>
      <span class="nav-item">Help</span>
    </a></li>

    <!-- Logout -->
    <li><a href="logout.php" class="logout">
      <i class="fas fa-sign-out-alt"></i>
      <span class="nav-item">Log out</span>
    </a></li>
  </ul>
</nav>




<section class="main">
<div class="main-top">
    <h1>Welcome, Dr. <?php echo htmlspecialchars($name); ?>!</h1>

<!-- Wrapper for Appointments and Profile/Settings Dropdowns -->
<div class="dropdown-wrapper" style="display: flex; align-items: center; justify-content: space-between;">

    <!-- Appointments Dropdown -->
    <div class="custom-dropdown-menu" style="margin-right: 20px;">
    <a href="#" class="custom-dropdown-toggle">
        <!-- Font Awesome Bell Icon -->
        <i class="fas fa-bell"></i>
    </a>
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
                            <small class="custom-media-meta">
                                <?php echo $row->AppointmentNumber; ?> at (<?php echo $row->ApplyDate; ?>)
                            </small>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="custom-dropdown-item text-center" style="color: gray;">
                No appointments available
            </div>
        <?php endif; ?>
    </div>
</div>

    <!-- Dropdown for Profile and Settings -->
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
    </section>


  </div>
</body>
</html>