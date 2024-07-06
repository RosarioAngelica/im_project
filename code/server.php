<?php
session_start();
require 'db.php';

// Handle different actions based on the 'action' parameter
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
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

// Function to handle login
function handleLogin($conn) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM organization WHERE username = ? AND password = ?");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['user'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
}

// Function to fetch events
function handleGetEvents($conn) {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $user = $_SESSION['user'];

    $stmt = $conn->prepare("SELECT * FROM event WHERE organizer_id = (SELECT id FROM organization WHERE username = ?)");
    $stmt->bind_param('s', $user);
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
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $user = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT id FROM organization WHERE username = ?");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $organizer = $result->fetch_assoc();

    if (!$organizer) {
        echo json_encode(['success' => false, 'message' => 'Organizer not found']);
        return;
    }

    $organizer_id = $organizer['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO event (title, description, date, organizer_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sssi', $title, $description, $date, $organizer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add event']);
    }
}

// Function to update an event
function handleUpdateEvent($conn) {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $user = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT id FROM organization WHERE username = ?");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $organizer = $result->fetch_assoc();

    if (!$organizer) {
        echo json_encode(['success' => false, 'message' => 'Organizer not found']);
        return;
    }

    $organizer_id = $organizer['id'];
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE event SET title = ?, description = ?, date = ? WHERE id = ? AND organizer_id = ?");
    $stmt->bind_param('sssii', $title, $description, $date, $event_id, $organizer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update event']);
    }
}

// Function to delete an event
function handleDeleteEvent($conn) {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $user = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT id FROM organization WHERE username = ?");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $organizer = $result->fetch_assoc();

    if (!$organizer) {
        echo json_encode(['success' => false, 'message' => 'Organizer not found']);
        return;
    }

    $organizer_id = $organizer['id'];
    $event_id = $_POST['event_id'];

    $stmt = $conn->prepare("DELETE FROM event WHERE id = ? AND organizer_id = ?");
    $stmt->bind_param('ii', $event_id, $organizer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
    }
}
?>
