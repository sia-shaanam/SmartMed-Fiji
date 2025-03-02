<?php
include('smf_db_conn.php');

// Capture form data
$role = $_POST['role'];
$user_id = $_POST['user_id'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Securely hash the password
$name = $_POST['name'];
$contact_information = $_POST['contact_information'];

// Check if the user_id already exists
$check_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$check_stmt->bind_param("s", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Display a JavaScript alert if user ID already exists
    echo "<script>
            alert('Error: User ID already exists. Please choose a different one.');
            window.history.back(); // Go back to the form
          </script>";
} else {
    // Insert the user into the users table
    $stmt = $conn->prepare("INSERT INTO users (role, user_id, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $role, $user_id, $password);

    if ($stmt->execute()) {
        // Insert into the corresponding table based on the role
        if ($role == 'doctor') {
            $doctor_stmt = $conn->prepare(
                "INSERT INTO doctors (doctor_id, user_id, name, contact_information, email, gender) 
                 VALUES (?, ?, ?, ?, '', 'other')"
            );
            $doctor_stmt->bind_param("ssss", $user_id, $user_id, $name, $contact_information);
            $doctor_stmt->execute(); // Execute the statement
            $doctor_stmt->close();
        } elseif ($role == 'admin') {
            $admin_stmt = $conn->prepare(
                "INSERT INTO admin (user_id, name, contact_information) 
                 VALUES (?, ?, ?)"
            );
            $admin_stmt->bind_param("sss", $user_id, $name, $contact_information);
            $admin_stmt->execute();
            $admin_stmt->close();
        } elseif ($role == 'nurse') { // Added logic for nurse registration
            $nurse_stmt = $conn->prepare(
                "INSERT INTO nurses (nurse_id, name, contact_information, password) 
                 VALUES (?, ?, ?, ?)"
            );
            $nurse_stmt->bind_param("ssss", $user_id, $name, $contact_information, $password);
            $nurse_stmt->execute(); // Execute the statement
            $nurse_stmt->close();
        }

        // Display a success alert and redirect to the admin dashboard
        echo "<script>
                alert('User registered successfully!');
                window.location.href = '../admin/doc_list/doc_list.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$check_stmt->close();
$conn->close();
?>
