<?php
// Include your database connection file
include('smf_db_conn.php');

// Initialize messages to avoid warnings
$success_message = '';
$error_message = '';

// Check if appointment_id exists in the URL
$appointment_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($appointment_id)) {
    // Get the updated remark from the form input
    $remark = isset($_POST['remark']) ? htmlspecialchars($_POST['remark']) : '';

    // Prepare the SQL query to update the remark for the specified appointment
    $query = "UPDATE appointments SET remark = ? WHERE appointment_id = ?";

    // Initialize a prepared statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the parameters to the statement
        $stmt->bind_param('si', $remark, $appointment_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Set a success message if the update was successful
            $success_message = "Remark updated successfully!";
        } else {
            // Set an error message if something went wrong
            $error_message = "Error updating remark: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Set an error message if the statement couldn't be prepared
        $error_message = "Error preparing query: " . $conn->error;
    }
}

// Retrieve the appointment information again for display purposes
if (!empty($appointment_id)) {
    $query = "SELECT * FROM appointments WHERE appointment_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error_message = "Error preparing query: " . $conn->error;
    }
} else {
    $error_message = "No appointment ID found.";
}

// Close the database connection at the very end
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your CSS file for styling -->
</head>
<style>
    /* General Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #b1ccfe; /* Light teal background */
        color: #333;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background-color: #ffffff; /* White background for the form */
        padding: 30px;
        border-radius: 10px;
    }

    /* Header Styles */
    header h2 {
        text-align: center;
        color: #2a5d67; /* Dark teal for header */
        font-size: 1.8rem;
        margin-bottom: 30px;
    }

    /* Form Section Titles */
    form h3 {
        color: #1abc9c; /* Light green for section titles */
        font-size: 1.4rem;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin: 25px 0 15px;
    }

    /* Flexbox for Form Rows */
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }

    /* Column Styles */
    .form-column {
        flex: 1;
        min-width: 300px;
        padding: 10px;
        border: 1px solid #f1f1f1;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    /* Label and Input Styles */
    label {
        margin-top: 10px;
        font-weight: bold;
        color: #34495e;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    select,
    textarea {
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #cfd8dc;
        border-radius: 4px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #1abc9c; /* Green border on focus */
        box-shadow: 0 0 5px rgba(26, 188, 156, 0.3);
    }

    /* Button Styles */
    button[type="submit"] {
        margin-top: 20px;
        padding: 12px;
        background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); /* Gradient button */
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
        background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%); /* Reverse gradient on hover */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .form-row {
            flex-direction: column;
        }

        button[type="submit"] {
            padding: 10px;
            font-size: 15px;
        }
    }

    .dashboard-btn {
        color: white;
        background-color: #20c997;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .dashboard-btn:hover {
        color: white;
        background-color: blue;
    }

    /* Style for read-only fields */
    input[readonly],
    textarea[readonly],
    select[readonly] {
        background-color: #f1f1f1;
        border-color: #ddd;
        color: #999;
    }

    /* Display error and success messages */
    .message {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
<body>

<div class="container">
    <h2>Edit Appointment</h2>
    <a href="doc_app_index.php" class="dashboard-btn">Go to Dashboard</a>

    <?php if ($success_message): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="edit-appointment.php?id=<?= htmlspecialchars($appointment['appointment_id']) ?>" method="POST">
        <div class="form-row">
            <div class="form-column">
                <!-- Appointment Information -->
                <h3>Appointment Information</h3>

                <label for="appointment_id">Appointment ID:</label>
                <input type="text" id="appointment_id" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>" readonly>

                <label for="patient_id">Patient ID:</label>
                <input type="text" id="patient_id" name="patient_id" value="<?= htmlspecialchars($appointment['patient_id']) ?>" readonly>

                <label for="patient_name">Patient Name:</label>
                <input type="text" id="patient_name" name="patient_name" value="<?= htmlspecialchars($appointment['patient_name']) ?>" readonly>

                <label for="appointment_date">Appointment Date:</label>
                <input type="date" id="appointment_date" name="appointment_date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" readonly>

                <label for="appointment_time">Appointment Time:</label>
                <input type="time" id="appointment_time" name="appointment_time" value="<?= htmlspecialchars($appointment['appointment_time']) ?>" readonly>

                <label for="doctor_name">Doctor:</label>
                <input type="text" id="doctor_name" name="doctor_name_display" value="<?= htmlspecialchars($appointment['name']) ?>" readonly>
                <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($appointment['doctor_id']) ?>">

                <label for="status">Status:</label>
                <input type="text" id="status" name="status" value="<?= htmlspecialchars($appointment['status']) ?>" readonly>

                <label for="reason">Reason:</label>
                <textarea id="reason" name="reason" readonly><?= htmlspecialchars($appointment['reason']) ?></textarea>
            </div>

            <div class="form-column">
                <!-- Update Remark -->
                <h3>Update Remark</h3>

                <label for="remark">Remark:</label>
                <textarea id="remark" name="remark"><?= htmlspecialchars($appointment['remark']) ?></textarea>
            </div>
        </div>

        <button type="submit">Update Remark</button>
    </form>
</div>

</body>
</html>

