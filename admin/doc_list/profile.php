<?php
session_start();
include('smf_db_conn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Check if doctor_id is set in the URL
if (!isset($_GET['doctor_id'])) {
    die('Doctor ID not specified.');
}

// Get doctor_id from the URL
$doctor_id = $_GET['doctor_id'];

// Prepare and execute the query to fetch doctor details
$sql = "SELECT doctor_id, user_id, name, specialization, contact_information, email, department, job_position, gender, age, image_url FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Query preparation failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $doctor_id);
$stmt->execute();
$stmt->bind_result($doctor_id, $user_id, $name, $specialization, $contact_information, $email, $department, $job_position, $gender, $age, $image_url);
$stmt->fetch();
$stmt->close();

// Close the database connection
$conn->close();

// Check if doctor details were found
if (!$name) {
    die('Doctor not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($name); ?>'s Profile</title>
    <link rel="stylesheet" href="../css/profile_styles.css"> <!-- Add your CSS for profile here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>

<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 20px;
        }

        .profile-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-card {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
        }

        .edit-button, .back-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .edit-button {
            background-color: #28a745;
        }

        .edit-button:hover {
            background-color: #218838;
        }

        .back-button {
            background-color: #007bff;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
<body>
    <div class="profile-container">
        <h1><?php echo htmlspecialchars($name); ?>'s Profile</h1>
        <div class="profile-card">
            <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Doctor Image" class="profile-image">
            <h2><?php echo htmlspecialchars($name); ?></h2>
            <p><strong>Doctor ID:</strong> <?php echo htmlspecialchars($doctor_id); ?></p>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($specialization); ?></p>
            <p><strong>Contact Information:</strong> <?php echo htmlspecialchars($contact_information); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
            <p><strong>Job Position:</strong> <?php echo htmlspecialchars($job_position); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></p>
        </div>
       <a href="doc_list.php" class="back-button">Back to Dashboard</a>
    </div>

</body>
</html>
