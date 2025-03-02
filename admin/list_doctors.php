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



// Search function logic
$search_query = "";
$search_results = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = $_POST['search'];

    // Modify the query to search by patient_id or first/last name
    $sql = "SELECT * FROM patient WHERE patient_id LIKE ? OR firstName LIKE ? OR lastName LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch search results
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }}


$sql = "SELECT * FROM patient";
$result = $conn->query($sql);
$all_patients = [];
while ($row = $result->fetch_assoc()) {
    $all_patients[] = $row;
}

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
        <li><a href="../app/doc_app_index.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Appointments</span></a></li>
        <li><a href="ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">EHR</span></a></li>
        <li><a href="analytics.php"><i class="fas fa-chart-line"></i><span class="nav-item">Patients</span></a></li>
        <li><a href="settings.php"><i class="fas fa-video"></i><span class="nav-item">Telemedicine</span></a></li>
        <li><a href="help.php"><i class="fas fa-file-invoice-dollar"></i><span class="nav-item">Billings</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <!-- Main section -->
    <section class="main">
      <div class="main-top" style="display: flex; align-items: center; justify-content: space-between;">
        <h1 style="text-align: center; font-size:36px; margin-bottom:20px;">EHR Records</h1>   
      </div>

      <div class="header">
</div>

<!-- Section 2: Search Bar and Add New Button -->
<div class="search-section">
    <div class="search-container">
        <form method="POST" id="search-form">
            <input type="text" name="search" id="search-input" placeholder="Search by patient ID or Name...">
            <button type="submit">Search</button>
        </form>
    </div>
    <button class="add-new-button" onclick="window.location.href='ehr_reg_pat.php'">Add New Record</button>
    
</div>

            

            <!-- Display Search Results -->
            <?php if (!empty($search_results)): ?>
                <div id="search-results" class="search-results-section">
                    <h3>Search Results:</h3>
                    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Patient ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td>
                                    <button class='edit-btn' onclick=\"window.location.href='update_pat.php?id={$row['patient_id']}'\">Edit</button>
                                    <button class='view-btn' onclick=\"location.href='view_patient.php?patient_id={$row['patient_id']}'\">View</button>
                                    </td>
                                    <td><?php echo $row['patient_id']; ?></td>
                                    <td><?php echo $row['firstName']; ?></td>
                                    <td><?php echo $row['lastName']; ?></td>
                                    <td><?php echo $row['dob']; ?></td>
                                    <td><?php echo $row['gender']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['phone']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button class="cancel-button" onclick="toggleSearchResults(false)">Cancel</button>
                </div>
            <?php endif; ?>

<!-- Section 3: Patient Records Table -->
<div class="table-section"></div>
<?php
// Database connection
include('smf_db_conn.php');

// Default query to retrieve data
$sql = "SELECT * FROM patient";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th>Actions</th>
                <th>Patient ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Preferred Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Emergency Contact Name</th>
                <th>Emergency Contact Number</th>
            </tr>
        </thead>
        <tbody>
    ';

    // Fetch and display each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>
                    <div class='action-buttons'>
                     <button class='Edit-btn' onclick=\"location.href='update_pat.php?patient_id={$row['patient_id']}'\">Edit</button>
                       
                        <button class='view-btn' onclick=\"location.href='view_patient.php?patient_id={$row['patient_id']}'\">View</button>
                    </div>
                </td>
                <td>{$row['patient_id']}</td>
                <td>{$row['firstName']}</td>
                <td>{$row['lastName']}</td>
                <td>{$row['preferredName']}</td>
                <td>{$row['dob']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['address']}</td>
                <td>{$row['emergencyContactName']}</td>
                <td>{$row['emergencyContactNumber']}</td>
            </tr>";
    }

    echo '
        </tbody>  
    </table>';
} else {
    echo "<p>No patient records found.</p>";
}

// Close connection
$conn->close();
?>


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

  



  </script>

</body>
</html>
