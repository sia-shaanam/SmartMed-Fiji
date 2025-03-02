<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Ensure script halts if not logged in
}

// Include database connection
include 'smf_db_conn.php';

// Check if patient_id is provided in the URL
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    // Validate if the patient exists in the 'patient' table
    $patient_sql = "SELECT firstName FROM patient WHERE patient_id = ?";
    $stmt = $conn->prepare($patient_sql);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a patient with this ID exists
    if ($result->num_rows > 0) {
        // Fetch patient details
        $patient = $result->fetch_assoc();

        // Handle form submission for sending the message
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $_POST['message']; // No validation for empty message

            // Insert the message into the 'messages' table
            $sql = "INSERT INTO messages (message_id, patient_id, user_id, message) VALUES (NULL, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $patient_id, $_SESSION['user_id'], $message);

            if ($stmt->execute()) {
                echo "Message sent successfully!";
            } else {
                // Display an error message if something goes wrong during insertion
                echo "Error sending message: " . $stmt->error;
            }
        }
    } else {
        // If patient_id doesn't exist, display an error message
        echo "Invalid patient ID. Please provide a valid patient.";
        exit();
    }
} else {
    // If no patient_id is provided in the URL, display an error message
    echo "No patient ID provided!";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message <?php echo htmlspecialchars($patient['firstName'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b1ccfe;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #20c997;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            background-color: #20c997;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #17a2b8;
        }
    </style>
</head>
<body>
<div class="container">
        <h1>Send Message to Patient</h1>
        <form action="" method="POST">
            <div>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <div>
                <button type="submit">Send Message</button>
            </div>
        </form>
    </div>
</body>
</html>
