<?php
// Database connection
include('smf_db_conn.php');

// Include FPDF library
require_once('fpdf.php'); // Adjust the path based on your directory structure

// Check if the form is submitted
// Check if the form is submitted
if (isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $appointment_id = $_POST['appointment_id'];
    $total_amount = 100; // Set the amount to $100 when status is 'Paid'

    // Update the billing status and total amount in the database
    if ($new_status === 'Paid') {
        $update_sql = "UPDATE billing SET status = ?, total_amount = ? WHERE appointment_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("sis", $new_status, $total_amount, $appointment_id);
    } else {
        $update_sql = "UPDATE billing SET status = ? WHERE appointment_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ss", $new_status, $appointment_id);
    }

    $stmt_update->execute();

    // Check if the update was successful
    if ($stmt_update->affected_rows > 0) {
        echo "<p style='color: white; background-color: #28a745; padding: 10px; text-align: center;'>Status updated successfully to $new_status. Total amount updated to $total_amount dollars if Paid.</p>";
    } else {
        echo "<p style='color: white; background-color: #dc3545; padding: 10px; text-align: center;'>Failed to update status.</p>";
    }
    $stmt_update->close();
}

// Initialize variables
$patient_name = $appointment_date = $appointment_time = $total_amount = $status = null;

// Check if appointment_id is set before proceeding
if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Fetch the appointment details
    $sql = "SELECT a.patient_name, a.appointment_date, a.appointment_time
            FROM appointments a
            WHERE a.appointment_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $patient_name = $row['patient_name'];
        $appointment_date = $row['appointment_date'];
        $appointment_time = $row['appointment_time'];

        // Now check for billing record
        $billing_sql = "SELECT total_amount, status FROM billing WHERE appointment_id = ?";
        $billing_stmt = $conn->prepare($billing_sql);
        $billing_stmt->bind_param("s", $appointment_id);
        $billing_stmt->execute();
        $billing_result = $billing_stmt->get_result();

        if ($billing_result->num_rows > 0) {
            // Fetch billing details
            $billing_row = $billing_result->fetch_assoc();
            $total_amount = $billing_row['total_amount'];
            $status = $billing_row['status'];
        } else {
            // No billing record exists, create one with default values
            $total_amount = 100; // Default amount
            $status = 'Unpaid'; // Default status
            
            $insert_billing_sql = "INSERT INTO billing (appointment_id, total_amount, status) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_billing_sql);
            $insert_stmt->bind_param("sis", $appointment_id, $total_amount, $status);
            $insert_stmt->execute();
            $insert_stmt->close();
        }

        // Output the bill details
        echo '
        <div style="max-width: 600px; margin: 40px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; font-size: 24px; margin-bottom: 20px; color: #333;">Invoice for Appointment ID: ' . $appointment_id . '</h2>
            <div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; background-color: #fafafa; border-radius: 8px;">
                <p style="font-size: 16px; color: #555;"><strong>Patient Name:</strong> ' . $patient_name . '</p>
                <p style="font-size: 16px; color: #555;"><strong>Appointment Date:</strong> ' . $appointment_date . '</p>
                <p style="font-size: 16px; color: #555;"><strong>Appointment Time:</strong> ' . $appointment_time . '</p>
                <p style="font-size: 16px; color: #555;"><strong>Total Amount:</strong> $' . $total_amount . '</p>
                <p style="font-size: 16px; color: #007bff; font-weight: bold;"><strong>Status:</strong> ' . $status . '</p>
            </div>
            <form style="text-align: center;" method="POST" action="">
                <input type="hidden" name="appointment_id" value="' . $appointment_id . '">
                <label for="status" style="display: block; margin-bottom: 8px; font-size: 16px; color: #333;">Change Billing Status:</label>
                <select name="status" id="status" style="padding: 10px; font-size: 16px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                    <option value="Unpaid"' . ($status === 'Unpaid' ? ' selected' : '') . '>Unpaid</option>
                    <option value="Paid"' . ($status === 'Paid' ? ' selected' : '') . '>Paid</option>
                </select>
                <input type="submit" name="update_status" value="Update Status" style="background-color: #28a745; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                <input type="submit" name="generate_pdf" value="Generate PDF Receipt" style="background-color: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px;">
            </form>
            <a href="bill_index.php" style="display: inline-block; text-align: center; margin-top: 20px; padding: 10px 20px; background-color: #6c757d; color: #fff; text-decoration: none; border-radius: 4px; font-size: 16px;">Back to Billing Index</a>
        </div>';

    } else {
        echo "<p style='color: white; background-color: #dc3545; padding: 10px; text-align: center;'>No appointment found for appointment ID: $appointment_id.</p>";
    }

    $stmt->close();
}
// Check if the PDF should be generated
if (isset($_POST['generate_pdf'])) {
    // Clear any previous output
    ob_clean(); // Use this to clean previous output buffer
    ob_start(); // Start output buffering

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Add hospital name
    $pdf->Cell(0, 10, 'Smart Med Fiji', 0, 1, 'C');
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Hospital Receipt', 0, 1, 'C');
    $pdf->Ln(10);

    // Draw a box for the receipt details
    $pdf->SetDrawColor(0, 0, 0); // Set border color to black
    $pdf->SetLineWidth(1); // Set line width
    $pdf->Rect(10, 40, 190, 100); // Draw rectangle (x, y, width, height)

    // Add receipt details inside the box
    $pdf->SetXY(15, 45); // Set position inside the box
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Patient Name: ' . $patient_name, 0, 1);
    $pdf->Cell(0, 10, 'Appointment ID: ' . $appointment_id, 0, 1);
    $pdf->Cell(0, 10, 'Appointment Date: ' . $appointment_date, 0, 1);
    $pdf->Cell(0, 10, 'Appointment Time: ' . $appointment_time, 0, 1);
    $pdf->Cell(0, 10, 'Total Amount: $' . $total_amount, 0, 1);
    $pdf->Cell(0, 10, 'Status: ' . $status, 0, 1);

    // Add a footer
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Thank you for choosing Smart Med Fiji!', 0, 1, 'C');
    $pdf->Cell(0, 10, 'This receipt is valid for record purposes.', 0, 1, 'C');

    // Output the PDF directly to the browser for download
    $pdf->Output('D', 'receipt_' . $appointment_id . '.pdf');

    // End output buffering and flush output buffer
    ob_end_flush();
    exit; // Terminate the script to prevent any further output
}


$conn->close();
?>
