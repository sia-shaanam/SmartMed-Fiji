<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartMed Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    body {
        background-color: #f0f4f8;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 50px auto;
        text-align: center;
    }

    .chart-container {
        margin: 20px 0;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        margin-bottom: 40px;
    }
</style>
<body>
    <div class="container">
        <h1>SmartMed Analytics Dashboard</h1>

        <!-- Appointment Chart -->
        <div class="chart-container">
            <canvas id="appointmentChart"></canvas>
        </div>

        <!-- Doctor Chart -->
        <div class="chart-container">
            <canvas id="doctorChart"></canvas>
        </div>

        <!-- Message Chart -->
        <div class="chart-container">
            <canvas id="messageChart"></canvas>
        </div>

        <!-- Patient Chart -->
        <div class="chart-container">
            <canvas id="patientChart"></canvas>
        </div>

        <!-- Telemedicine Chart -->
        <div class="chart-container">
            <canvas id="telemedicineChart"></canvas>
        </div>

        <!-- Prescription Chart -->
        <div class="chart-container">
            <canvas id="prescriptionChart"></canvas>
        </div>
    </div>

    <script>
        // Appointment Chart
        fetch('fetch_appointments.php')
            .then(response => response.json())
            .then(data => {
                const statuses = data.map(item => item.status);
                const totals = data.map(item => item.total);

                new Chart(document.getElementById('appointmentChart').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: statuses,
                        datasets: [{
                            label: 'Appointment Status',
                            data: totals,
                            backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
                        }]
                    }
                });
            });

        // Doctor Chart
        fetch('fetch_doctors.php')
            .then(response => response.json())
            .then(data => {
                const doctorNames = data.map(item => item.name);
                const totalAppointments = data.map(item => item.total_appointments);

                new Chart(document.getElementById('doctorChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: doctorNames,
                        datasets: [{
                            label: 'Appointments per Doctor',
                            data: totalAppointments,
                            backgroundColor: '#2196F3'
                        }]
                    }
                });
            });

        // Message Chart
        fetch('fetch_messages.php')
            .then(response => response.json())
            .then(data => {
                const dates = data.map(item => item.date);
                const messageCounts = data.map(item => item.message_count);

                new Chart(document.getElementById('messageChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Messages Over Time',
                            data: messageCounts,
                            borderColor: '#673AB7',
                            fill: false
                        }]
                    }
                });
            });

        // Patient Chart
        fetch('fetch_patients.php')
            .then(response => response.json())
            .then(data => {
                const categories = data.map(item => item.category);
                const counts = data.map(item => item.count);

                new Chart(document.getElementById('patientChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Patient Categories',
                            data: counts,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                        }]
                    }
                });
            });

        // Telemedicine Chart
        fetch('fetch_telemedicine.php')
            .then(response => response.json())
            .then(data => {
                const dates = data.map(item => item.date);
                const sessions = data.map(item => item.sessions);

                new Chart(document.getElementById('telemedicineChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Telemedicine Sessions',
                            data: sessions,
                            borderColor: '#FF5722',
                            fill: false
                        }]
                    }
                });
            });

        // Prescription Chart
        fetch('fetch_prescriptions.php')
            .then(response => response.json())
            .then(data => {
                const medicationNames = data.map(item => item.medication);
                const prescriptionCounts = data.map(item => item.count);

                new Chart(document.getElementById('prescriptionChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: medicationNames,
                        datasets: [{
                            label: 'Prescriptions Issued',
                            data: prescriptionCounts,
                            backgroundColor: '#9C27B0'
                        }]
                    }
                });
            });
    </script>
</body>
</html>
