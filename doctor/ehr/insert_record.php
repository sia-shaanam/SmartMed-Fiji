<?php
// Include your database connection
include('smf_db_conn.php');

// Collect form data
$patient_id = $_POST['patient_id'];

// Check if the patient_id exists in the patient table
$check_query = "SELECT patient_id FROM patient WHERE patient_id = '$patient_id'";
$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    // If patient exists, proceed with inserting the record
    $data = $_POST;
    $fields = array_keys($data);
    $values = array_map(function($value) use ($conn) {
        return "'" . $conn->real_escape_string($value) . "'";
    }, array_values($data));

    $sql = "INSERT INTO medical_records (" . implode(", ", $fields) . ") 
            VALUES (" . implode(", ", $values) . ")";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('New record created successfully!');
                window.location.href = 'ehr_main.php';  // Redirect to the form page
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $conn->error . "');
                window.history.back();  // Go back to the form
              </script>";
    }
} else {
    // If patient_id doesn't exist, show an error message
    echo "<script>
            alert('Error: Patient ID not found. Please enter a valid Patient ID.');
            window.history.back();  // Go back to the form
          </script>";
}

$conn->close();
?>
