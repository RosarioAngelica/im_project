document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        dateClick: function(info) {
            document.getElementById('date').value = info.dateStr;
            document.getElementById('modal').style.display = 'block';
        }
    });

    calendar.render();

    var modal = document.getElementById('modal');
    var span = document.getElementsByClassName('close')[0];

    span.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    document.getElementById('reservationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(event.target);
        var data = {};
        formData.forEach((value, key) => (data[key] = value));

        fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                endpoint: 'reservations',
                organization_name: data.organization_name,
                event_name: data.event_name,
                description: data.description,
                date: data.date,
                time_start: data.time_start,
                time_end: data.time_end
            }),
        })
        .then(response => response.json())
        .then(data => {
            alert('Reservation made successfully!');
            modal.style.display = 'none';
            document.getElementById('event_id').value = data.event_id;
            document.getElementById('attendeeModal').style.display = 'block';
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });

    var attendeeModal = document.getElementById('attendeeModal');
    var attendeeSpan = attendeeModal.getElementsByClassName('close')[0];

    attendeeSpan.onclick = function() {
        attendeeModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == attendeeModal) {
            attendeeModal.style.display = 'none';
        }
    }

    document.getElementById('attendeeForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(event.target);
        var data = {};
        formData.forEach((value, key) => (data[key] = value));

        fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                endpoint: 'attendees',
                event_id: data.event_id,
                first_name: data.first_name,
                last_name: data.last_name,
                contact_no: data.contact_no,
                course: data.course,
                section: data.section,
                gender: data.gender,
                attendees_no: data.attendees_no
            }),
        })
        .then(response => response.json())
        .then(data => {
            alert('Attendee registered successfully!');
            attendeeModal.style.display = 'none';
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
});
