<?php
include '../../config/config.php';


// Database connection setup
$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}
$pdo = new PDO('mysql:host=localhost;dbname=db_ba3101', 'root', '');

$checkQuery = "SELECT * FROM tb_attendees WHERE event_id = :event_id AND student_id = :student_id";
$checkStmt = $pdo->prepare($checkQuery);
$checkStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$checkStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
$checkStmt->execute();
$attendeeExists = $checkStmt->rowCount() > 0;
$queryParams = $_SERVER['QUERY_STRING'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attend'])) {
    if (!$attendeeExists) {
        $query = "INSERT INTO tb_attendees (event_id, student_id) VALUES (:event_id, :student_id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: event.php?" . $queryParams);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    $deleteQuery = "DELETE FROM tb_attendees WHERE event_id = :event_id AND student_id = :student_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
    $deleteStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
    $deleteStmt->execute();
    $_SESSION['delete_action_completed'] = true;
    header("Location: event.php?" . $queryParams);

    exit;
}
try {
    // Retrieve event_id from the URL parameters
    $event_id = $_GET['event_id'];

    // Prepare SQL query to get details of the specific event
$event_query = $conn->prepare("SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, COALESCE(i.image_filename, 'default-image.jpg') AS header_image, d.department_name 
                                FROM tb_event e
                                INNER JOIN tb_department d ON e.department_id = d.department_id
                                LEFT JOIN tb_event_images i ON e.event_id = i.event_id
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
    <link rel="stylesheet" href="../../styles/student.css">
    <link rel="stylesheet" href="../../styles/rso.css">
    <style>

    </style>
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>
    <div class="headerImg">
        <img src="../images/<?php echo $header_image; ?>" alt="Event Image">

    </div>
    <main>
        <div class="event-details-container">

            <div class="event-details-header">
                <h2>
                    <?php echo ucwords($event_title); ?>
                </h2>

                <div class="event-details-content">

                </div>
                <h4 class="details">
                    Details
                    </h3>

                    <p id="details">
                        <?php echo $event_detail; ?>
                    </p>
                    <div class="department">
                        <p>Department:

                        </p>
                        <?php echo ucwords($department_name); ?>
                    </div>
                    <div id="date">
                        <img src="../../images/calendar.png" alt="">
                        <h3>
                            <?php echo $event_date; ?>
                        </h3>
                    </div>
                    <div class="status">
                        <span class="<?php echo $statusClass; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>
            </div>


            <div class="event-details-container">

                <section class="tableContainer">
                    <h2>Attendees List</h2>

                    <?php
         
            $attendeesQuery = $conn->prepare("SELECT tbstudinfo.firstname, tbstudinfo.lastname, tbstudinfo.course
                                             FROM tb_attendees
                                             INNER JOIN tbstudinfo ON tb_attendees.student_id = tbstudinfo.studid
                                             WHERE tb_attendees.event_id = ?");
            $attendeesQuery->bind_param('i', $event_id);
            $attendeesQuery->execute();
            $attendeesResult = $attendeesQuery->get_result();
            $attendees = $attendeesResult->fetch_all(MYSQLI_ASSOC);

            if (!empty($attendees)) {
              
                include '../../components/table.component.php';
                $head = array('Name', 'Course');
                $body = array();

                foreach ($attendees as $attendee) {
                    $name = $attendee['firstname'] . ' ' . $attendee['lastname'];
                    $course = $attendee['course'];
                    $body[] = array($name, $course);
                }

                createTable($head, $body);
            } else {
                echo "<p>No attendees for this event.</p>";
            }
            ?>
                </section>


            </div>
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
    </main>
    <footer>

        <div class="footerCont">
            <div id="dtl">
                <div class="dat">
                    <img src="../../images/calendar.png" alt="">
                    <h3>
                        <?php echo $event_date; ?>
                    </h3>
                </div>
                <div class="titl">
                    <h2>
                        <?php echo ucwords($event_title); ?>
                    </h2>
                </div>
            </div>
            <div class="btnCont">
                <div class="events_button">
                    <form action="" method="POST">

                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                        <?php
                        $attendeeExists = false;
                        $checkStmt->execute();
                        $attendeeExists = $checkStmt->rowCount() > 0;
                        if ($statusClass == 'upcoming-event' || $statusClass == 'ongoing-event') {
                            if ($attendeeExists) {
                                echo '<button type="button" onclick="openCancelModal(' . $event_id . ')" id="cancelbtn">Cancel</button>';
                            } else {
                                echo '<button type="button" onclick="openAttendModal(' . $event_id . ')">Interested</button>';
                            }
                        }
                        ?>
                    </form>
                </div>
            </div>

        </div>
    </footer>

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
</body>

</html>