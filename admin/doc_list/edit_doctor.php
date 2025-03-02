<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional CSS for styling -->
</head>
<style>
       body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4; /* Light background color */
            color: #333; /* Dark text color */
        }

        h1, h2 {
            color:  #003366; /* Green color for headings */
            text-align: center;
        }

        form {
            background-color: white; /* White background for the form */
            padding: 20px; /* Padding around the form */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
            max-width: 800px; /* Max width of the form */
            margin: 0 auto; /* Center the form */
        }

        .form-row {
            display: flex; /* Use flexbox for the row */
            flex-wrap: wrap; /* Allow items to wrap */
            margin-bottom: 15px; /* Space between rows */
        }

        .form-column {
            flex: 1; /* Each column will take equal space */
            min-width: 250px; /* Minimum width for each column */
            margin-right: 10px; /* Space between columns */
        }

        .form-column:last-child {
            margin-right: 0; /* Remove right margin for the last column */
        }

        label {
            display: block; /* Make labels block-level elements */
            margin: 5px 0; /* Spacing around labels */
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            width: 100%; /* Full width */
            padding: 10px; /* Padding inside the input fields */
            border: 1px solid #ccc; /* Light border */
            border-radius: 4px; /* Slightly rounded corners */
            font-size: 16px; /* Font size */
        }

        button {
            padding: 10px 15px; /* Button padding */
            background-color:  #003366; /* Button background color */
            color: white; /* Button text color */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s ease; /* Transition effect */
        }

        button:hover {
            background-color: #45a049; /* Darker shade on hover */
        }

        .error-message {
            color: red; /* Error message color */
            text-align: center; /* Center the error message */
        }
        .back-button {
            background-color: #007bff;
            color: white; /* Button text color */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s ease; /* Transition effect */
            padding: 10px 15px;
       
        }

        .back-button:hover {
            background-color: #0056b3;
        }
</style>
<body>
    <h1>Edit Doctor Information</h1>
    <form action="edit_doctor.php" method="POST">
        <label for="doctor_id">Doctor ID:</label>
        <input type="text" id="doctor_id" name="doctor_id" required>
        <button type="submit">Fetch Doctor Info</button>
    </form>

    <?php
    // Database connection
    $host = 'localhost'; // Your database host
    $db = 'smf_db'; // Your database name
    $user = 'root'; // Your database username
    $pass = ''; // Your database password

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch doctor info if doctor_id is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id'])) {
        $doctor_id = $_POST['doctor_id'];

        // Fetch the doctor's data
        $sql = "SELECT * FROM doctors WHERE doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $doctor = $result->fetch_assoc();
            ?>
            <h2>Edit Doctor Information</h2>
            <form action="update_doctor.php" method="POST">
                <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor['doctor_id']); ?>">
                

                
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>

                <label for="specialization">Specialization:</label>
                <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>">

                <label for="contact_information">Contact Information:</label>
                <input type="text" id="contact_information" name="contact_information" value="<?php echo htmlspecialchars($doctor['contact_information']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>

                <label for="department">Department:</label>
                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($doctor['department']); ?>">

                <label for="job_position">Job Position:</label>
                <input type="text" id="job_position" name="job_position" value="<?php echo htmlspecialchars($doctor['job_position']); ?>">

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male" <?php if ($doctor['gender'] === 'male') echo 'selected'; ?>>Male</option>
                    <option value="female" <?php if ($doctor['gender'] === 'female') echo 'selected'; ?>>Female</option>
                    <option value="other" <?php if ($doctor['gender'] === 'other') echo 'selected'; ?>>Other</option>
                </select>

                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($doctor['age']); ?>" min="0">

                <button type="submit">Update Doctor Info</button>
            </form>
            <a href="doc_list.php" class="back-button">Back to Dashboard</a>
  
            <?php
        } else {
            echo "<p>No doctor found with that ID.</p>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>
</body>
</html>
