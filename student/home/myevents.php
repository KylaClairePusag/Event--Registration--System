<?php
session_start(); // Start the session

include '../../config/config.php';

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$pdo = new PDO('mysql:host=localhost;dbname=db_ba3101', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    // Redirect to login page or handle the case when the user is not logged in
    header("Location: login.php");
    exit;
}

// Check attendance for the logged-in student
$checkQuery = "SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name
                FROM tb_event e
                INNER JOIN tb_department d ON e.department_id = d.department_id
                INNER JOIN tb_attendees a ON e.event_id = a.event_id
                WHERE a.student_id = :student_id";
$checkStmt = $pdo->prepare($checkQuery);
$checkStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
$checkStmt->execute();
$attendedEvents = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attend'])) {
    if (!$attendeeExists) {
        $query = "INSERT INTO tb_attendees (event_id, student_id) VALUES (:event_id, :student_id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: myevents.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    $deleteQuery = "DELETE FROM tb_attendees WHERE event_id = :event_id AND student_id = :student_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
    $deleteStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
    $deleteStmt->execute();

    $_SESSION['delete_action_completed'] = true;

    header("Location: myevents.php");
    exit;
}

// Fetch events data
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Page</title>
    <link rel="stylesheet" href="../../styles/student.css">
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <div class="box-container">
            <?php foreach ($attendedEvents as $attendedEvent) { ?>
                <?php
                // Extract attended event details
                $event_id = $attendedEvent["event_id"];
                $event_title = $attendedEvent["event_title"];
                $event_detail = $attendedEvent["event_detail"];
                $event_date = date('F j, Y', strtotime($attendedEvent["event_date"]));
                $status = $attendedEvent["status"];
                $header_image = $attendedEvent["header_image"];
                $department_name = $attendedEvent["department_name"];

                // Count attendees for the attended event
                $attendees_query = $conn->prepare("SELECT COUNT(*) as attendee_count FROM tb_attendees WHERE event_id = ?");
                $attendees_query->bind_param('i', $event_id);
                $attendees_query->execute();
                $attendees_row = $attendees_query->get_result()->fetch_assoc();
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
                        break;
                }
                ?>

                <div class="event-box <?php echo $statusClass; ?>-box">
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
                    <div class="events_button">
                        <form action="" method="POST">
                            <button type="button" id="viewBtn"
                                onclick="window.location.href='event.php?event_id=<?php echo $event_id; ?>'">View
                                Event</button>
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                            <?php
                            if ($statusClass == 'upcoming-event' || $statusClass == 'ongoing-event') {
                                echo '<button type="button" onclick="openCancelModal(' . $event_id . ')" id="cancelbtn">Cancel</button>';
                            }
                            ?>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- The Cancel Dialog -->
        <dialog id="cancelDialog" class="modal">
            <div class="modal-content">
                <button class="close" onclick="closeCancelDialog()">&times;</button>
                <h2>Are you sure you want to cancel your attendance for this event?</h2>
                <form action="" method="POST">
                    <input type="hidden" name="event_id" id="cancelDialogEventId" value="">
                    <div class="clearfix">
                        <button type="button" onclick="closeCancelDialog()" class="cancelbtn">No</button>
                        <button type="submit" name="cancel">Yes</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            function openCancelModal(eventId) {
                document.getElementById('cancelDialogEventId').value = eventId;
                document.getElementById('cancelDialog').showModal();
            }

            function closeCancelDialog() {
                document.getElementById('cancelDialog').close();
            }

            document.addEventListener('click', function (event) {
                var cancelModal = document.getElementById('cancelDialog');
                if (event.target === cancelModal) {
                    cancelModal.close();
                }
            });
        </script>
    </main>
</body>

</html>