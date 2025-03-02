<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmed Appointments - Compact Calendar</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js'></script>

    <style>
        #calendar {
            max-width: 600px; /* Smaller calendar width */
            margin: 0 auto;
        }

        /* Style for the popup (modal) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px; /* Max width of popup */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Compact Confirmed Appointments Calendar</h1>

<div id="calendar"></div>

<!-- Popup Modal -->
<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Appointment Details</h2>
        <p><strong>Patient Name:</strong> <span id="patientName"></span></p>
        <p><strong>Doctor ID:</strong> <span id="doctorId"></span></p>
        <p><strong>Reason:</strong> <span id="reason"></span></p>
        <p><strong>Remark:</strong> <span id="remark"></span></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridWeek', // Set initial view to week for more compactness
        height: 'auto', // Automatically adjust the calendar height
        events: 'fetch-confirmed-appointments.php', // Fetch confirmed appointments
        headerToolbar: { // Simplified toolbar
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridDay,dayGridWeek'
        },
        eventClick: function(info) {
            // Show the modal with the event details
            var modal = document.getElementById("appointmentModal");
            var span = document.getElementsByClassName("close")[0];

            // Populate the modal with event data
            document.getElementById('patientName').textContent = info.event.title;
            document.getElementById('doctorId').textContent = info.event.extendedProps.doctor_id;
            document.getElementById('reason').textContent = info.event.extendedProps.reason;
            document.getElementById('remark').textContent = info.event.extendedProps.remark;

            // Display the modal
            modal.style.display = "block";

            // Close the modal when clicking on the 'x'
            span.onclick = function() {
                modal.style.display = "none";
            }

            // Close the modal when clicking outside the modal content
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    });

    calendar.render();
});
</script>

</body>
</html>
