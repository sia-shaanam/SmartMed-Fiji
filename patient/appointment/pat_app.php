
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #e0e0e0;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1c40f;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Appointments</h1>

    <?php
    // Check if there are any appointments
    if (mysqli_num_rows($result) > 0) {
        echo "<table>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Doctor ID</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Remark</th>
                </tr>";

        // Loop through the results and display them in a table
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $row['appointment_id'] . "</td>
                    <td>" . $row['patient_name'] . "</td>
                    <td>" . $row['appointment_date'] . "</td>
                    <td>" . $row['appointment_time'] . "</td>
                    <td>" . $row['doctor_id'] . "</td>
                    <td>" . $row['status'] . "</td>
                    <td>" . $row['reason'] . "</td>
                    <td>" . $row['remark'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-data'>No appointments found.</div>";
    }

    // Close database connection
    mysqli_close($conn);
    ?>

</div>

</body>
</html>
