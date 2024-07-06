<?php
require 'db.php';

// Handle different actions based on the 'action' parameter
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'get_events':
        handleGetEvents($conn);
        break;
    case 'add_event':
        handleAddEvent($conn);
        break;
    case 'update_event':
        handleUpdateEvent($conn);
        break;
    case 'delete_event':
        handleDeleteEvent($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Function to fetch events
function handleGetEvents($conn) {
    $stmt = $conn->prepare("SELECT * FROM event");
    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    echo json_encode(['success' => true, 'events' => $events]);
}

// Function to add a new event
function handleAddEvent($conn) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO event (title, description, date) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $title, $description, $date);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add event']);
    }
}

// Function to update an event
function handleUpdateEvent($conn) {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE event SET title = ?, description = ?, date = ? WHERE id = ?");
    $stmt->bind_param('sssi', $title, $description, $date, $event_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update event']);
    }
}

// Function to delete an event
function handleDeleteEvent($conn) {
    $event_id = $_POST['event_id'];

    $stmt = $conn->prepare("DELETE FROM event WHERE id = ?");
    $stmt->bind_param('i', $event_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
    }
}
?>
