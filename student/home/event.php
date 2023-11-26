<?php
include '../../config/config.php';


// Database connection setup
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
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attend'])) {
    if(!$attendeeExists) {
        $query = "INSERT INTO tb_attendees (event_id, student_id) VALUES (:event_id, :student_id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
        $stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: event.php?".$queryParams);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    $deleteQuery = "DELETE FROM tb_attendees WHERE event_id = :event_id AND student_id = :student_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
    $deleteStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
    $deleteStmt->execute();
    $_SESSION['delete_action_completed'] = true;
    header("Location: event.php?".$queryParams);

    exit;
}
try {
    // Retrieve event_id from the URL parameters
    $event_id = $_GET['event_id'];

    // Prepare SQL query to get details of the specific event
    $event_query = $conn->prepare("SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name FROM tb_event e
                                   INNER JOIN tb_department d ON e.department_id = d.department_id
                                   WHERE e.event_id = ?");
    $event_query->bind_param('i', $event_id);

    if(!$event_query->execute()) {
        throw new Exception("Query failed: ".$event_query->error);
    }

    // Fetch the result
    $event_result = $event_query->get_result();
    $event_row = $event_result->fetch_assoc();

    // Check if the event exists
    if(!$event_row) {
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
    <link rel="stylesheet" href="../../styles/student.css">
    <link rel="stylesheet" href="../../styles/rso.css">
    <style>

    </style>
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <section id="head" class="event-details-container">
            <div class="event-details-header">
                <h2>
                    <?php echo ucwords($event_title); ?>
                </h2>
                <div class="department">
                    <p>Hosted by:

                    </p>
                    <?php echo ucwords($department_name); ?>
                </div>

                <div class="event-details-nav">
                    <a href="#" data-target="details" class="active-link">Details</a>
                    <a href="#" data-target="attendees">Attendees</a>
                    <a href="#" data-target="images">Images</a>
                </div>


        </section>
        <section class="event-details-container toggle-section active-section" id="details">


            <div class="headerImg">
                <img src="../../<?php echo $header_image; ?>" alt="Event Image">

            </div>

            <h4 class="details">
                Details
            </h4>

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
            </div>
        </section>
        <section class="tableContainer toggle-section" id="attendees">

            <?php
            $attendeesQuery = $conn->prepare("SELECT 
                         tbstudinfo.firstname AS student_firstname, 
                         tbstudinfo.lastname AS student_lastname, 
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

            // Custom sorting function to prioritize teachers
            usort($attendees, function ($a, $b) {
                if(!empty($a['emp_firstname']) && empty($b['emp_firstname'])) {
                    return -1; // $a is a teacher, $b is not
                } elseif(empty($a['emp_firstname']) && !empty($b['emp_firstname'])) {
                    return 1; // $b is a teacher, $a is not
                } else {
                    return 0; // Both are either teachers or not
                }
            });

            if(!empty($attendees)) {
                echo '<div class="attendees-grid">';
                foreach($attendees as $attendee) {
                    echo '<div class="attendee-card">';

                    if(!empty($attendee['student_firstname'])) {
                        // If the attendee is a student
                        $name = $attendee['student_firstname'].' '.$attendee['student_lastname'];
                        $course = $attendee['course'];
                        $profile = $attendee['student_profile'];

                        // Use a default image if the profile image path is empty
                        $imgSrc = !empty($profile) ? "../../$profile" : "../../images/alt.png";

                        echo '<img src="'.$imgSrc.'" alt="'.$name.' - '.$course.'">';
                        echo '<p>'.$name.' (Student)</p>';
                    } elseif(!empty($attendee['emp_firstname'])) {
                        // If the attendee is an employee
                        $name = $attendee['emp_firstname'].' '.$attendee['emp_lastname'];
                        $course = $attendee['department'];

                        // Use a default image if the profile image path is empty
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




        <!-- Add this section after the attendees section -->
        <section class="imageContainer toggle-section" id="images">

            <div class="image-grid">
                <?php
                // Fetch event images data
                $imagesQuery = $conn->prepare("SELECT image_filename FROM tb_event_images WHERE event_id = ?");
                $imagesQuery->bind_param('i', $event_id);
                $imagesQuery->execute();
                $imagesResult = $imagesQuery->get_result();
                $images = $imagesResult->fetch_all(MYSQLI_ASSOC);

                if(!empty($images)) {
                    // Display images in a grid
                    foreach($images as $image) {
                        echo '<div class="image-item"><img src="../../'.$image['image_filename'].'" alt="Event Image"></div>';
                    }
                } else {
                    echo "<p>No images for this event.</p>";
                }
                ?>
            </div>
        </section>



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
                        if($statusClass == 'upcoming-event' || $statusClass == 'ongoing-event') {
                            if($attendeeExists) {
                                echo '<button type="button" onclick="openCancelModal('.$event_id.')" id="cancelbtn">Cancel</button>';
                            } else {
                                echo '<button type="button" onclick="openAttendModal('.$event_id.')">Interested</button>';
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

        document.addEventListener('click', function (event) {
            var modal = document.querySelector('.modal');
            if (event.target === modal) {
                modal.close();
            }
        });
        document.addEventListener('click', function (event) {
            var cancelModal = document.getElementById('cancelDialog');
            if (event.target === cancelModal) {
                cancelModal.close();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Add click event listeners to navigation links
            var navLinks = document.querySelectorAll('.event-details-nav a');
            navLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    var targetId = link.getAttribute('data-target');
                    showSection(targetId);
                    setActiveLink(link);
                });
            });

            // Function to show or hide sections
            function showSection(sectionId) {
                var sections = document.querySelectorAll('.toggle-section');
                sections.forEach(function (section) {
                    if (section.id === sectionId) {
                        section.classList.add('active-section');
                    } else {
                        section.classList.remove('active-section');
                    }
                });
            }

            // Function to set active link styling
            function setActiveLink(clickedLink) {
                navLinks.forEach(function (link) {
                    link.classList.remove('active-link');
                });
                clickedLink.classList.add('active-link');
            }
        });
    </script>
</body>

</html>