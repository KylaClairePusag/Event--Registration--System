<?php
include '../../config/config.php';

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if($conn->connect_error) {
    die('Connection Failed: '.$conn->connect_error);
}
$pdo = new PDO('mysql:host=localhost;dbname=db_ba3101', 'root', '');

$checkQuery = "SELECT * FROM tb_attendees WHERE event_id = :event_id AND student_id = :student_id";
$checkStmt = $pdo->prepare($checkQuery);
$checkStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$checkStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
$checkStmt->execute();
$attendeeExists = $checkStmt->rowCount() > 0;
$queryParams = $_SERVER['QUERY_STRING'];



try {
    $event_id = $_GET['event_id'];

    $event_query = $conn->prepare("SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name FROM tb_event e
                                   INNER JOIN tb_department d ON e.department_id = d.department_id
                                   WHERE e.event_id = ?");
    $event_query->bind_param('i', $event_id);

    if(!$event_query->execute()) {
        throw new Exception("Query failed: ".$event_query->error);
    }

    $event_result = $event_query->get_result();
    $event_row = $event_result->fetch_assoc();

    if(!$event_row) {
        throw new Exception("Event not found");
    }

    $event_title = ucwords($event_row["event_title"]);
    $event_detail = $event_row["event_detail"];
    $event_date = date('F j, Y', strtotime($event_row["event_date"]));
    $status = $event_row["status"];
    $header_image = $event_row["header_image"];
    $department_name = ucwords($event_row["department_name"]);

    $statusClass = '';
    switch($status) {
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
    echo "Error: ".$ex->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
    <link rel="stylesheet" href="../../styles/student.css">
    <style></style>
</head>

<body>
    <?php include '../../components/rsoHeader.php'; ?>
    <main>
        <section id="head" class="event-details-container">
            <div class="event-details-header">
                <h2>
                    <?php echo $event_title; ?>
                </h2>
                <div class="department">
                    <p>Hosted by:</p>
                    <?php echo $department_name; ?>
                </div>
                <div class="event-details-nav">
                    <a href="#" data-target="details" class="active-link">Details</a>
                    <a href="#" data-target="attendees">Attendees</a>
                    <a href="#" data-target="images">Images</a>
                </div>
            </div>
        </section>
        <section class="event-details-container toggle-section active-section" id="details">
            <div class="headerImg">
                <img src="../../<?php echo $header_image; ?>" alt="Event Image">
            </div>
            <h4 class="details">Details</h4>
            <p id="details">
                <?php echo $event_detail; ?>
            </p>
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
        </section>
        <section class="tableContainer toggle-section" id="attendees">
            <?php
            $attendeesQuery = $conn->prepare("SELECT tbstudinfo.firstname AS student_firstname, 
                                             tbstudinfo.lastname AS student_lastname, 
                                             tbstudinfo.studid AS student_studid, 
                                             tbstudinfo.course, 
                                             tbstudentaccount.student_profile AS student_profile,
                                             tbempinfo.firstname AS emp_firstname, 
                                             tbempinfo.lastname AS emp_lastname, 
                                             tbempinfo.department,
                                             tbempaccount.emp_profile AS emp_profile
                                FROM tb_attendees
                                LEFT JOIN tbstudinfo ON tb_attendees.student_id = tbstudinfo.studid
                                LEFT JOIN tbstudentaccount ON tb_attendees.student_id = tbstudentaccount.studid
                                LEFT JOIN tbempinfo ON tb_attendees.empid = tbempinfo.empid
                                LEFT JOIN tbempaccount ON tb_attendees.empid = tbempaccount.empid
                                WHERE tb_attendees.event_id = ?");
            $attendeesQuery->bind_param('i', $event_id);
            $attendeesQuery->execute();
            $attendeesResult = $attendeesQuery->get_result();
            $attendees = $attendeesResult->fetch_all(MYSQLI_ASSOC);

            usort($attendees, function ($a, $b) {
                if(!empty($a['emp_firstname']) && empty($b['emp_firstname'])) {
                    return -1;
                } elseif(empty($a['emp_firstname']) && !empty($b['emp_firstname'])) {
                    return 1;
                } else {
                    return 0;
                }
            });

            if(!empty($attendees)) {
                echo '<div class="attendees-grid">';
                foreach($attendees as $attendee) {
                    echo '<div class="attendee-card" onclick="redirectToProfile('.$attendee['student_studid'].')" >';
                    if(!empty($attendee['student_firstname'])) {
                        $name = $attendee['student_firstname'].' '.$attendee['student_lastname'];
                        $course = $attendee['course'];
                        $profile = $attendee['student_profile'];
                        $studid = isset($attendee['student_studid']) ? $attendee['student_studid'] : '';
                        $imgSrc = !empty($profile) ? "../../$profile" : "../../images/alt.png";
                        echo '<img src="'.$imgSrc.'" alt="'.$name.' - '.$course.'">';
                        echo '<p>'.$name.' (Student)</p>';
                    } elseif(!empty($attendee['emp_firstname'])) {
                        $name = $attendee['emp_firstname'].' '.$attendee['emp_lastname'];
                        $course = $attendee['department'];
                        $imgSrc = !empty($attendee['emp_profile']) ? "../../".$attendee['emp_profile'] : "../../images/alt.png";
                        echo '<img src="'.$imgSrc.'" alt="'.$name.' - '.$course.'">';
                        echo '<p>'.$name.' (Teacher)</p>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo "<p>No attendees for this event.</p>";
            }
            ?>
        </section>
        <section class="imageContainer toggle-section" id="images">
            <div class="image-grid">
                <?php
                $imagesQuery = $conn->prepare("SELECT image_filename FROM tb_event_images WHERE event_id = ?");
                $imagesQuery->bind_param('i', $event_id);
                $imagesQuery->execute();
                $imagesResult = $imagesQuery->get_result();
                $images = $imagesResult->fetch_all(MYSQLI_ASSOC);

                if(!empty($images)) {
                    foreach($images as $image) {
                        echo '<div class="image-item"  onclick="zoomImage(this)"><img src="../../'.$image['image_filename'].'" alt="Event Image"></div>';
                    }
                } else {
                    echo "<p>No images for this event.</p>";
                }
                ?>
            </div>
        </section>

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
                        <?php echo $event_title; ?>
                    </h2>
                </div>
            </div>

        </div>
    </footer>

    <script src="../../script/events.js"></script>
</body>

</html>