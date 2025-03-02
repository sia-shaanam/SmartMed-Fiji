// JavaScript for doctor dashboard interaction

document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar navigation
    const toggleBtn = document.getElementById('toggle-btn');
    const navBar = document.querySelector('.nav-bar');
    
    toggleBtn.addEventListener('click', () => {
      navBar.classList.toggle('active');
    });
  
    // Modal functionality
    const modal = document.getElementById("appointmentModal");
    const span = document.getElementsByClassName("close")[0];
    const appointmentTable = document.getElementById('appointmentTable');
  
    if (appointmentTable) {
      const viewDetailsBtns = appointmentTable.querySelectorAll('.viewDetailsBtn');
      
      viewDetailsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const row = this.closest('tr');
          const appointmentData = JSON.parse(row.dataset.appointment);
          
          // Populate modal with appointment details
          document.getElementById("modalPatientName").textContent = appointmentData.patient_name;
          document.getElementById("modalDoctorName").textContent = appointmentData.doctor_name;
          document.getElementById("modalReason").textContent = appointmentData.reason;
          document.getElementById("modalRemark").textContent = appointmentData.remark;
          
          // Display the modal
          modal.style.display = "block";
        });
      });
    }
  
    // Close the modal when user clicks on <span> (x)
    span.onclick = function() {
      modal.style.display = "none";
    }
  
    // Close the modal when user clicks anywhere outside of the modal
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  
    // Initialize FullCalendar for doctor's schedule
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [
          // Sample events, these should be loaded dynamically from the database
          {
            title: 'Consultation - John Doe',
            start: '2024-10-22T10:00:00',
            end: '2024-10-22T11:00:00'
          },
          {
            title: 'Follow-up - Jane Smith',
            start: '2024-10-23T12:00:00',
            end: '2024-10-23T12:30:00'
          }
        ]
      });
      calendar.render();
    }
  });
  