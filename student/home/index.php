<?php
// Database connection setup
include '../../config/config.php';
session_start();

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

try {
    // Retrieve department_id from the session
    $departmentId = $_SESSION['department_id'];

    // Prepare SQL query with department_id condition and join with tb_department
    $query = $conn->prepare("SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name FROM tb_event e
                            INNER JOIN tb_department d ON e.department_id = d.department_id
                            WHERE e.department_id = ?");
    $query->bind_param('i', $departmentId);

    if (!$query->execute()) {
        throw new Exception("Query failed: " . $query->error);
    }

    // Fetch results
    $result = $query->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/student.css">
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <div class="box-container">
            <?php
            foreach ($rows as $row) {
                $event_id = $row["event_id"];
                $event_title = $row["event_title"];
                $event_detail = $row["event_detail"];
                $event_date = date('F j, Y', strtotime($row["event_date"]));
                $status = $row["status"];
                $header_image = $row["header_image"];
                $department_name = $row["department_name"];

                $attendees_query = $conn->prepare("SELECT COUNT(*) as attendee_count FROM tb_attendees WHERE event_id = ?");
                $attendees_query->bind_param('i', $event_id);
                $attendees_query->execute();
                $attendees_result = $attendees_query->get_result();
                $attendees_row = $attendees_result->fetch_assoc();
                $attendee_count = $attendees_row['attendee_count'];

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
                ?>

                <div class="event-box">
                    <div class="header">
                        <img src="../../<?php echo $header_image; ?>" alt="Event Image">
                    </div>
                    <div class="event-content">
                        <h2>
                            <?php echo ucwords($event_title); ?>
                        </h2>
                        <div id="date">
                            <img src="../../images/calendar.png" alt="">
                            <h3>
                                <?php echo $event_date; ?>
                            </h3>
                        </div>
                        <p id="details">
                            <?php echo $event_detail; ?>
                        </p>
                        <p>Status: <span class="<?php echo $statusClass; ?>">
                                <?php echo $status; ?>
                            </span></p>
                        <p>Department:
                            <?php echo ucwords($department_name); ?>
                        </p>
                        <p>Attendees:
                            <?php echo $attendee_count; ?>
                        </p>
                    </div>
                </div>

            <?php } ?>

        </div>
    </main>
</body>

</html>