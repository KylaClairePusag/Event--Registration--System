<?php
session_start();
include '../../config/config.php';

if(!isset($_SESSION['student_email'])) {
    header("Location: ../signin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_ba3101";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error) {
    die("Connection failed: ".$conn->connect_error);
}

$empid = isset($_GET['empid']) ? $_GET['empid'] : null;

if(!$empid) {
    die("Error: empid not provided in the URL");
}

$sqlTeacher = "SELECT tbempinfo.lastname, tbempinfo.firstname, tb_department.department_name, tbempaccount.emp_email, tbempaccount.emp_profile
        FROM tbempinfo
        JOIN tbempaccount ON tbempinfo.empid = tbempaccount.empid
        LEFT JOIN tb_department ON tbempaccount.department_id = tb_department.department_id
        WHERE tbempinfo.empid = ?";
$stmtTeacher = $conn->prepare($sqlTeacher);

if(!$stmtTeacher) {
    die("Error preparing the query: ".$conn->error);
}

$stmtTeacher->bind_param("i", $empid);
$stmtTeacher->execute();

if($stmtTeacher->errno) {
    die("Error executing the query: ".$stmtTeacher->error);
}

$stmtTeacher->store_result();

if($stmtTeacher->num_rows > 0) {
    $stmtTeacher->bind_result($lastname, $firstname, $department_name, $emp_email, $emp_profile);
    $stmtTeacher->fetch();
    $stmtTeacher->close();

    $sqlEventsAttended = "SELECT tb_event.event_id, tb_event.event_title, tb_event.event_detail, tb_event.event_date, tb_event.header_image, tb_event.status, tb_department.department_name as department, COUNT(tb_attendees.empid) as attendee_count
        FROM tb_event
        LEFT JOIN tb_attendees ON tb_event.event_id = tb_attendees.event_id
        LEFT JOIN tb_department ON tb_event.department_id = tb_department.department_id
        WHERE tb_attendees.empid = ?
        GROUP BY tb_event.event_id";
    $stmtEventsAttended = $conn->prepare($sqlEventsAttended);

    if(!$stmtEventsAttended) {
        die("Error preparing the query: ".$conn->error);
    }

    $stmtEventsAttended->bind_param("i", $empid);
    $stmtEventsAttended->execute();

    if($stmtEventsAttended->errno) {
        die("Error executing the query: ".$stmtEventsAttended->error);
    }

    $resultEvents = $stmtEventsAttended->get_result();

    if($resultEvents->num_rows > 0) {
        $eventsAttended = array();
        while($row = $resultEvents->fetch_assoc()) {
            $statusClass = '';
            switch($row['status']) {
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

            $eventsAttended[] = array(
                'event_id' => $row['event_id'],
                'event_title' => $row['event_title'],
                'event_detail' => $row['event_detail'],
                'event_date' => $row['event_date'],
                'header_image' => $row['header_image'],
                'status' => $row['status'],
                'department' => $row['department'],
                'attendee_count' => $row['attendee_count'],
                'statusClass' => $statusClass,
            );
        }
    }
} else {
    echo "Teacher not found";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Information</title>
    <style>
        .attended-event {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
    <link rel="stylesheet" href="../../styles/rso.css">
    <link rel="stylesheet" href="../../styles/student.css">
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <section class="profile-sec">
            <?php
            if(isset($empid)) {
                ?>
                <div class="profile-header">
                </div>
                <div class="profile-info">
                    <?php
                    if($emp_profile) {
                        ?>
                        <img class="profile-image" src="../../<?php echo $emp_profile; ?>" alt="Profile Image">
                        <?php
                    } else {
                        ?>
                        <p class="profile-image">No image available</p>
                        <?php
                    }
                    ?>
                    <div class="info">
                        <div class="studinfo">
                            <p id="username">
                                <?php echo "$firstname $lastname"; ?>
                            </p>
                            <p class="stud">Teacher at Batangas State University</p>
                            <p id="dept">
                                Department
                                <?php echo $department_name; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </section>

        <h1 class="events-h">Attended Events</h1>

        <div class="box-container">
            <?php
            if(isset($eventsAttended)) {
                foreach($eventsAttended as $event) {
                    ?>
                    <div class="event-box <?php echo $event['statusClass']; ?>-box">
                        <div class="header">
                            <img src="../../<?php echo $event['header_image']; ?>" alt="Event Image">
                        </div>
                        <div class="event-content">
                            <h2>
                                <?php echo ucwords($event['event_title']); ?>
                            </h2>
                            <div id="date">
                                <img src="../../images/calendar.png" alt="">
                                <h3>
                                    <?php echo $event['event_date']; ?>
                                </h3>
                            </div>
                            <p id="details">
                                <?php echo $event['event_detail']; ?>
                            </p>
                            <p>Department:
                                <?php echo $event['department']; ?>
                            </p>
                            <p>Status: <span class="<?php echo $event['statusClass']; ?>">
                                    <?php echo ucwords($event['status']); ?>
                                </span></p>
                            <p>Attendees:
                                <?php echo $event['attendee_count']; ?>
                            </p>
                        </div>
                        <div class="events_button">
                            <button type="button" id="viewBtn"
                                onclick="window.location.href='event.php?event_id=<?php echo $event['event_id']; ?>'">View
                                Event</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>No events attended.</p>
                <?php
            }
            ?>
        </div>
    </main>
</body>

</html>