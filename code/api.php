// api.php
<?php
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
$db = new SQLite3('events.db');

switch ($method) {
    case 'GET':
        if (isset($_GET['endpoint']) && $_GET['endpoint'] == 'events') {
            $result = $db->query('SELECT * FROM event');
            $events = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $events[] = $row;
            }
            echo json_encode($events);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['endpoint']) && $data['endpoint'] == 'reservations') {
            $organization_name = $data['organization_name'];
            $event_name = $data['event_name'];
            $description = $data['description'];
            $date = $data['date'];
            $time_start = $data['time_start'];
            $time_end = $data['time_end'];

            // Insert organization
            $db->exec("INSERT INTO organization (name) VALUES ('$organization_name')");
            $organization_id = $db->lastInsertRowID();

            // Insert event
            $db->exec("INSERT INTO event (organization_id, name, description) VALUES ($organization_id, '$event_name', '$description')");
            $event_id = $db->lastInsertRowID();

            // Insert schedule
            $db->exec("INSERT INTO schedule (schedule_id, date, time_start, time_end, organization_id) VALUES ('$event_id', '$date', '$time_start', '$time_end', '$organization_id')");

            echo json_encode(['status' => 'success', 'event_id' => $event_id]);
        } elseif (isset($data['endpoint']) && $data['endpoint'] == 'attendees') {
            $event_id = $data['event_id'];
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $contact_no = $data['contact_no'];
            $course = $data['course'];
            $section = $data['section'];
            $gender = $data['gender'];
            $attendees_no = $data['attendees_no'];

            $db->exec("INSERT INTO attendees (attendees_id, first_name, last_name, contact_no, course, section, gender, attendees_no, organization_id) VALUES ('$event_id', '$first_name', '$last_name', '$contact_no', '$course', '$section', '$gender', '$attendees_no', '$event_id')");

            echo json_encode(['status' => 'success', 'attendee_id' => $db->lastInsertRowID()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}
?>
