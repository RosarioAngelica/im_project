document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById('login-form');
    const loginSection = document.getElementById('login-section');
    const calendarSection = document.getElementById('calendar-section');
    const eventForm = document.getElementById('event-form');
    const logoutButton = document.getElementById('logout');

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(loginForm);
        formData.append('action', 'login');

        fetch('server.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loginSection.style.display = 'none';
                calendarSection.style.display = 'block';
                loadEvents();
            } else {
                alert(data.message || 'Login failed');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    logoutButton.addEventListener('click', () => {
        loginSection.style.display = 'block';
        calendarSection.style.display = 'none';
        fetch('server.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'logout' }),
        });
    });

    eventForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const formData = new FormData(eventForm);
        formData.append('action', formData.get('event_id') ? 'update_event' : 'add_event');

        fetch('server.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadEvents();
                eventForm.reset();
            } else {
                alert(data.message || 'Failed to save event');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    function loadEvents() {
        const formData = new FormData();
        formData.append('action', 'get_events');

        fetch('server.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayEvents(data.events);
            } else {
                alert(data.message || 'Failed to load events');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function displayEvents(events) {
        const calendar = document.getElementById('calendar');
        calendar.innerHTML = '';

        events.forEach(event => {
            const eventDiv = document.createElement('div');
            eventDiv.classList.add('event');
            eventDiv.innerHTML = `
                <h3>${event.title}</h3>
                <p>${event.description}</p>
                <p><strong>Date:</strong> ${event.date}</p>
                <button onclick="editEvent(${event.id}, '${event.title}', '${event.description}', '${event.date}')">Edit</button>
                <button onclick="deleteEvent(${event.id})">Delete</button>
            `;
            calendar.appendChild(eventDiv);
        });
    }

    window.editEvent = (id, title, description, date) => {
        document.getElementById('event_id').value = id;
        document.getElementById('title').value = title;
        document.getElementById('description').value = description;
        document.getElementById('date').value = date;
    }

    window.deleteEvent = (id) => {
        if (confirm('Are you sure you want to delete this event?')) {
            const formData = new FormData();
            formData.append('action', 'delete_event');
            formData.append('event_id', id);

            fetch('server.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadEvents();
                } else {
                    alert(data.message || 'Failed to delete event');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
});
