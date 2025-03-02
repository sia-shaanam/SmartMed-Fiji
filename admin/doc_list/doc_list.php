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
    .main {
  padding: 20px;
  flex: 1;
  margin-left: 10px; /* Leave space for the sidebar */
  width: calc(100% - 250px); /* Take up the rest of the width */
  height: 100vh; /* Full height viewport */
  overflow-y: auto;
}
.main.collapsed {
  margin-left: 80px; /* Adjust main content when sidebar is collapsed */
  width: calc(100% - 80px); /* Take up the rest of the width when sidebar is collapsed */
  transition:ease-in 2ms;
}
      .dashboard {
        width: 1280px;
        background-color: white;
        padding: 40px;
        border-radius: 10px;
    }

    /* Container */
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        padding: 20px;
        background-color: #f4f6f9;
    }

    /* Card Styling */
    .card {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    }

    /* Doctor Image */
    .card img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        margin-bottom: 15px;
        object-fit: cover;
    }

    /* Card Content */
    .card-content {
        text-align: center;
    }

    .card-content h3 {
        font-size: 20px;
        margin-bottom: 8px;
        color: #333333;
    }

    .card-content p {
        font-size: 14px;
        color: #555555;
        margin-bottom: 5px;
    }

    /* Actions Button Styling */
    .actions {
        margin-top: 10px;
        display: flex;
        gap: 10px;
    }

    .actions a {
        text-decoration: none;
    }

    .actions button {
        padding: 8px 16px;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        transition: background-color 0.3s ease;
    }

    /* Button Colors */
    .actions button {
        background-color: #007bff;
    }

    .actions a[href="edit.html"] button {
        background-color: #28a745;
    }

    /* Hover Effect for Buttons */
    .actions button:hover {
        opacity: 0.9;
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
        <li><a href="doc_list.php"><i class="fas fa-users"></i><span class="nav-item">User Management</span></a></li>
        <li><a href="../ehr/ehr_main.php"><i class="fas fa-file-medical"></i><span class="nav-item">Patients</span></a></li>
        <li><a href="../app/doc_app_index.php"><i class="fas fa-file-medical"></i><span class="nav-item">Appointments</span></a></li>
       <li><a href="../backup/index.php"><i class="fas fa-chart-line"></i><span class="nav-item">Backup & Recovery</span></a></li>
        <li><a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Log out</span></a></li>
      </ul>
    </nav>

    <!-- Main section -->
    <section class="main">
    <div class="main-content">
            

            <div class="dashboard">
                <h2>Our Doctors!</h2>
                <p>
    <a href="edit_doctor.php" style="
        display: inline-block; 
        padding: 10px 20px; 
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
        onmouseout="this.style.backgroundColor='#003366';">
        Edit
    </a>
    <a href="../../back_login/register.html" style="
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
        onmouseout="this.style.backgroundColor='#003366 ';">
        Register Doctor
    </a>
    <a href="photo.html" style="
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
        onmouseout="this.style.backgroundColor='#003366';">
        Add Photo
    </a>
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
</p>

<!-- Cards container -->
<div class="cards-container">
    <?php
   include('smf_db_conn.php');

    // Fetch doctors from the database
    $sql = "SELECT doctor_id, name, specialization, age, department, image_url FROM doctors";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo '<div class="card">';
            // Display the image stored in the `image_url` field
            echo '<img src="' . $row["image_url"] . '" alt="Doctor Image">';
            echo '<div class="card-content">';
            echo '<h3>' . $row["name"] . '</h3>';
            echo '<p>Specialization: ' . $row["specialization"] . '</p>';
            echo '<p>ID: ' . $row["doctor_id"] . '</p>';
            echo '<p>Age: ' . $row["age"] . '</p>';
            echo '<p>Department: ' . $row["department"] . '</p>';
            echo '<div class="actions">';
            echo '<a href="profile.php?doctor_id=' . $row["doctor_id"] . '"><button>Profile</button></a>';
           echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "No doctors found";
    }
    $conn->close();
    ?>
</div>
</div>

</div>
       
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
