<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Include database connection
include 'smf_db_conn.php';  // Assuming you have a connection script

// Fetch patient_id from the query parameter
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    // Fetch patient details
    $patient_sql = "SELECT * FROM patient WHERE patient_id = ?";
    $stmt = $conn->prepare($patient_sql);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $patient_result = $stmt->get_result();
    $patient = $patient_result->fetch_assoc();

    // Fetch medical records details
    $medical_sql = "SELECT * FROM medical_records WHERE patient_id = ?";
    $stmt = $conn->prepare($medical_sql);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $medical_result = $stmt->get_result();
    $medical_records = $medical_result->fetch_assoc();
} else {
    echo "No patient ID provided!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #20c997;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .user-name {
            font-size: 16px;
            font-weight: bold;
        }
        
        main {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .profile-section {
            display: flex;
            flex-direction: column;
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        
        .user-details h2 {
            margin-bottom: 10px;
        }
        
        .reset-password {
            display: inline-block;
            background-color: #20c997;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .contact-details {
            text-align: right;
        }
        
        .personal-info {
            display: flex;
            justify-content: space-between;
        }
        
        .info-card {
            width: 48%;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .info-card h3 {
            margin-bottom: 15px;
            color: #20c997;
        }
        
        .social-connect, .device-info {
            margin-top: 20px;
        }
        
        .social-connect h3, .device-info h3 {
            margin-bottom: 10px;
        }
        
        .social-connect a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            color: #20c997;
        }
        
        .device-info p {
            margin-bottom: 5px;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">SmartMed Fiji</div>
            <div class="user-info">
                <img src="user-avatar.jpg" alt="User Avatar" class="avatar">
                <span class="user-name">Vikas Raj Yadav</span>
            </div>
        </header>

        <main>
            <section class="profile-section">
                <div class="profile-header">
                    <img src="user-avatar.jpg" alt="Profile Picture" class="profile-pic">
                    <div class="user-details">
                        <h2>Vikas Raj Yadav</h2>
                        <p>Student, Vision Institute of Technology</p>
                        <p>Year: 2009 to 2015</p>
                        <a href="#" class="reset-password">Reset Password</a>
                    </div>
                    <div class="contact-details">
                        <p><strong>Email:</strong> rajyadav@clinirex.com</p>
                        <p><strong>Phone:</strong> 8604205001</p>
                        <p><strong>ID:</strong> 09P02301</p>
                    </div>
                </div>

                <div class="personal-info">
                    <div class="info-card">
                        <h3>Personal Details</h3>
                        <p><strong>Gender:</strong> Male</p>
                        <p><strong>Date of Birth:</strong> 24 July 1994</p>
                        <p><strong>Blood Group:</strong> A</p>
                        <p><strong>Registered Email:</strong> vikasyad@gmail.com</p>
                        <p><strong>Permanent Address:</strong> 64, Sarbari Road, New Azad Nagar, Kanpur, Uttar Pradesh - 208011</p>
                        <p><strong>Residential Address:</strong> 867, Himalaya Road, Sec-38, Gurgaon, Haryana - 122009</p>
                    </div>
                    <div class="info-card">
                        <h3>Academia</h3>
                        <p><strong>Masters:</strong> Vision Institute of Pharmacy, 80%, 2015-2018 <a href="#">Attach file</a></p>
                        <p><strong>Bachelors:</strong> Vision Institute of Pharmacy, 80%, 2009-2015 <a href="#">Attach file</a></p>
                        <p><strong>Intermediate:</strong> Pt. RPM Inter College, 80%, 2007-2009 <a href="#">Attach file</a></p>
                        <p><strong>High School:</strong> Priyadarshini Public School, 80%, 2006-2007 <a href="#">Attach file</a></p>
                    </div>
                </div>

                <div class="social-connect">
                    <h3>Social Network</h3>
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">LinkedIn</a>
                    <a href="#">YouTube</a>
                </div>

                <div class="device-info">
                    <h3>Devices</h3>
                    <p><strong>CliniRex Check:</strong> Moto G4 Plus (Active)</p>
                    <p><strong>IMEI:</strong> 523819928581</p>
                    <p><strong>Last active:</strong> 27 June 2018, 08:29 AM</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>






