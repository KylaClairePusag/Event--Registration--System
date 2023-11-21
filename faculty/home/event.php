<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
include '../../config/config.php';


// Database connection setup
$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

try {
    // Retrieve event_id from the URL parameters
    $event_id = $_GET['event_id'];

    // Prepare SQL query to get details of the specific event
    $event_query = $conn->prepare("SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name FROM tb_event e
                                   INNER JOIN tb_department d ON e.department_id = d.department_id
                                   WHERE e.event_id = ?");
    $event_query->bind_param('i', $event_id);

    if (!$event_query->execute()) {
        throw new Exception("Query failed: " . $event_query->error);
    }

    // Fetch the result
    $event_result = $event_query->get_result();
    $event_row = $event_result->fetch_assoc();

    // Check if the event exists
    if (!$event_row) {
        throw new Exception("Event not found");
    }

    // Extract event details
    $event_title = $event_row["event_title"];
    $event_detail = $event_row["event_detail"];
    $event_date = date('F j, Y', strtotime($event_row["event_date"]));
    $status = $event_row["status"];
    $header_image = $event_row["header_image"];
    $department_name = $event_row["department_name"];

    // Define a CSS class based on the status
    $statusClass = '';
    switch ($status) {
        case 'upcoming':
            $statusClass = 'upcoming-event';
            break;
        case 'ongoing':
            $statusClass = 'ongoing-event';
            break;
        case 'ended':
            $statusClass = 'ended-event';
            break;
        default:
            $statusClass = 'default-event';
            break;
    }
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/faculty.css">
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>
    <?php include '../../components/facultyHeader.php'; ?>

    <main>
        <div class="event-details-container">
            <div class="event-details-header">
                <img src="../../<?php echo $header_image; ?>" alt="Event Image">
                <h2>
                    <?php echo ucwords($event_title); ?>
                </h2>
                <p>Status: <span class="<?php echo $statusClass; ?>">
                        <?php echo $status; ?>
                    </span></p>
            </div>
            <div class="event-details-content">
                <div id="date">
                    <img src="../../images/calendar.png" alt="">
                    <h3>
                        <?php echo $event_date; ?>
                    </h3>
                </div>
                <p id="details">
                    <?php echo $event_detail; ?>
                </p>
                <p>Department:
                    <?php echo ucwords($department_name); ?>
                </p>
            </div>
        </div>
    </main>
</body>

</html>
