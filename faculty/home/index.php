<?php

include '../../config/config.php';

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$pdo = new PDO('mysql:host=localhost;dbname=db_ba3101', 'root', '');

$checkQuery = "SELECT * FROM tb_attendees WHERE event_id = :event_id AND empid = :empid";
$checkStmt = $pdo->prepare($checkQuery);
$checkStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$checkStmt->bindParam(':empid', $_SESSION['empid'], PDO::PARAM_INT);
$checkStmt->execute();
$attendeeExists = $checkStmt->rowCount() > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attend'])) {
    if (!$attendeeExists) {
        $query = "INSERT INTO tb_attendees (event_id, empid) VALUES (:event_id, :empid)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
        $stmt->bindParam(':empid', $_SESSION['empid'], PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    $deleteQuery = "DELETE FROM tb_attendees WHERE event_id = :event_id AND empid = :empid";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
    $deleteStmt->bindParam(':empid', $_SESSION['empid'], PDO::PARAM_INT);
    $deleteStmt->execute();

    $_SESSION['delete_action_completed'] = true;

    header("Location: index.php");
    exit;
}

// Fetch events data
try {
    // Retrieve department_id from the session
    $departmentId = $_SESSION['empid'];

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
    <link rel="stylesheet" href="../../styles/emp.css">
    <link rel="stylesheet" href="../../styles/rso.css">

</head>

<body>
    <?php include '../../components/facultyHeader.php'; ?>

    <main>
        <div class="box-container">
            <?php foreach ($rows as $row) { ?>
            <div class="event-box">
                <?php
                    // Extract event details
                    $event_id = $row["event_id"];
                    $event_title = $row["event_title"];
                    $event_detail = $row["event_detail"];
                    $event_date = date('F j, Y', strtotime($row["event_date"]));
                    $status = $row["status"];
                    $header_image = $row["header_image"];
                    $department_name = $row["department_name"];

                    // Count attendees for the event
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
                            $statusClass = 'default-event';
                            break;
                    }
                    ?>

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
                            // Move the attendee check inside the loop
                            $attendeeExists = false; // Default to false for each event
                            $checkStmt->execute();
                            $attendeeExists = $checkStmt->rowCount() > 0;

                            if ($attendeeExists) {
                                echo '<button type="button" onclick="openCancelModal(' . $event_id . ')" id="cancelbtn">Cancel</button>';
                            } else {
                                echo '<button type="button" onclick="openAttendModal(' . $event_id . ')">Interested</button>';
                            }
                            ?>
                    </form>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- The Attend Dialog -->
        <dialog id="attendDialog" class="modal">
            <div class="modal-content">

                <button class="close" onclick="closeCancelDialog()">&times;</button>
                <h2>Are you sure you want to attend this event?</h2>
                <form action="" method="POST">
                    <input type="hidden" name="event_id" id="attendDialogEventId" value="">
                    <div class="clearfix">
                        <button type="button" onclick="closeAttendDialog()" class="cancelbtn">No</button>

                        <button type="submit" name="attend">Yes</button>


                    </div>

                </form>
            </div>
        </dialog>

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
        function openAttendModal(eventId) {
            document.getElementById('attendDialogEventId').value = eventId;
            document.getElementById('attendDialog').showModal();
        }

        function closeAttendDialog() {
            document.getElementById('attendDialog').close();
        }

        function openCancelModal(eventId) {
            document.getElementById('cancelDialogEventId').value = eventId;
            document.getElementById('cancelDialog').showModal();
        }

        function closeCancelDialog() {
            document.getElementById('cancelDialog').close();
        }

        document.addEventListener('click', function(event) {
            var modal = document.querySelector('.modal');
            if (event.target === modal) {
                modal.close();
            }
        });
        document.addEventListener('click', function(event) {
            var cancelModal = document.getElementById('cancelDialog');
            if (event.target === cancelModal) {
                cancelModal.close();
            }
        });
        </script>

    </main>

</body>

</html>