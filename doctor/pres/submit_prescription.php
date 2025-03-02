<?php
// Database connection parameters
include('smf_db_conn.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $prescription = $_POST['prescription'];

    // Validate input
    if (empty($patient_id) || empty($doctor_id) || empty($prescription)) {
        die("Please fill in all fields.");
    }

    // Prepare and bind the SQL statement to insert prescription
    $sql = "INSERT INTO prescriptions (patient_id, doctor_id, prescription) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $patient_id, $doctor_id, $prescription);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted ID to use for PDF generation
        $prescription_id = $stmt->insert_id;

        // Fetch doctor's name
        $sqlDoctor = "SELECT name FROM doctors WHERE doctor_id = ?";
        $stmtDoctor = $conn->prepare($sqlDoctor);
        $stmtDoctor->bind_param("s", $doctor_id);
        $stmtDoctor->execute();
        $stmtDoctor->bind_result($doctor_name);
        $stmtDoctor->fetch();
        $stmtDoctor->close();

        // Fetch patient's name
        $sqlPatient = "SELECT firstName, lastName FROM patient WHERE patient_id = ?";
        $stmtPatient = $conn->prepare($sqlPatient);
        $stmtPatient->bind_param("s", $patient_id);
        $stmtPatient->execute();
        $stmtPatient->bind_result($firstName, $lastName);
        $stmtPatient->fetch();
        $stmtPatient->close();

        // HTML for displaying prescription details
        echo "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f9f9f9;
                    color: #333;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 30px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                }
                h1 {
                    color: #007bff;
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th, td {
                    padding: 12px;
                    text-align: left;
                }
                th {
                    background-color: #007bff;
                    color: white;
                }
                td {
                    background-color: #f9f9f9;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 20px 0;
                    text-align: center;
                }
                .btn:hover {
                    background-color: #0056b3;
                }
                a {
                    color: #007bff;
                    text-decoration: none;
                }
                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Prescription Submitted</h1>
                <table>
                    <tr>
                        <th>Patient ID</th>
                        <td>" . htmlspecialchars($patient_id) . "</td>
                    </tr>
                    <tr>
                        <th>Patient Name</th>
                        <td>" . htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) . "</td>
                    </tr>
                    <tr>
                        <th>Prescribed by</th>
                        <td>" . htmlspecialchars($doctor_name) . "</td>
                    </tr>
                    <tr>
                        <th>Prescription</th>
                        <td>" . nl2br(htmlspecialchars($prescription)) . "</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>" . date("Y-m-d H:i:s") . "</td>
                    </tr>
                </table>

                <!-- Link to generate PDF -->
                <p><a href='generate_pdf.php?id=$prescription_id' class='btn'>Download Prescription as PDF</a></p>
                <p><a href='pres_index.html'>Go back</a></p>
            </div>
        </body>
        </html>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
}

$conn->close();
?>
