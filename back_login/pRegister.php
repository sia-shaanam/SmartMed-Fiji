<?php
// Include your database connection
include('smf_db_conn.php');

// Function to generate a unique patient ID
function generatePatientID() {
    return 'PAT' . uniqid();  // Generate a unique patient ID starting with 'PAT'
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = $_POST['login_id'];  
    $password = $_POST['password'];
    $patientName = $_POST['name'];
    $age = $_POST['age'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if login_id already exists
    $stmt = $conn->prepare("SELECT * FROM login WHERE login_id = ?");
    $stmt->bind_param("s", $login_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If login_id exists, show an error
        echo "Error: The login ID already exists. Please choose a different login ID.";
    } else {
        // Generate a unique patient ID
        $patient_id = generatePatientID();

        // Insert into the login table
        $stmt = $conn->prepare("INSERT INTO login (login_id, password, patient_id, name, age) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssi", $login_id, $hashed_password, $patient_id, $patientName, $age);
            if ($stmt->execute()) {
                // Now insert into the patient table using the same login_id and generated patient_id
                $stmt_patient = $conn->prepare("INSERT INTO patient (patient_id, login_id, firstName, age) VALUES (?, ?, ?, ?)");
                if ($stmt_patient) {
                    $firstName = $patientName; 
                    $stmt_patient->bind_param("sssi", $patient_id, $login_id, $firstName, $age);
                    if ($stmt_patient->execute()) {
                        echo "User Login have been saved successfully!";
                    } else {
                        echo "Error: Could not save patient data. " . $stmt_patient->error;
                    }
                } else {
                    echo "Error: Failed to prepare patient statement. " . $conn->error;
                }
            } else {
                echo "Error: Could not save user data. " . $stmt->error;
            }
        } else {
            echo "Error: Failed to prepare login statement. " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<style>
    body {
    background: linear-gradient(135deg, #dbe6f6,#00aaff);
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

form {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

form h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: bold;
}

input[type="text"],
input[type="password"],
input[type="number"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.8);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    font-size: 16px;
    color: #333;
}

input[type="submit"] {
    width: 100%;
    padding: 10px;
    background: #9599e2;
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

input[type="submit"]:hover {
    background: linear-gradient(135deg, #9599e2, white);
}

::placeholder {
    color: #999;
    font-size: 14px;
}

form label,
form input {
    margin-top: 10px;
}
.login-btn {
    display: inline-block;
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    color: #333;
    font-size: 16px;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.login-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    color: #555;
    box-shadow: 0 6px 40px rgba(0, 0, 0, 0.15);
}

</style>
<body>
<body>
    <!-- HTML form for input -->
    <form method="post" action="">
        <label for="login_id">Login ID:</label>  
        <input type="text" name="login_id" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        
        <label for="name">Patient Name:</label>
        <input type="text" name="name" required>
        
        <label for="age">Age:</label>
        <input type="number" name="age" required>
        
        <input type="submit" value="Submit" class="login-btn">
        <a href="login.html" class="login-btn">Login now</a>
    </form>
</body>
</html>

   
</body>
</html>
