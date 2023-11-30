<?php
session_start();

include '../../config/config.php';

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');

$profileQuery = "SELECT tbempinfo.lastname, tbempinfo.firstname, tb_department.department_name, tbempaccount.emp_email, tbempaccount.emp_profile
        FROM tbempinfo
        JOIN tbempaccount ON tbempinfo.empid = tbempaccount.empid
        LEFT JOIN tb_department ON tbempaccount.department_id = tb_department.department_id
        WHERE tbempinfo.empid = ?";
$profileStmt = $pdo->prepare($profileQuery);
$profileStmt->bindParam(1, $_SESSION['empid'], PDO::PARAM_INT);
$profileStmt->execute();
$profileData = $profileStmt->fetch(PDO::FETCH_ASSOC);

$checkQuery = "SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name
                FROM tb_event e
                INNER JOIN tb_department d ON e.department_id = d.department_id
                INNER JOIN tb_attendees a ON e.event_id = a.event_id
                WHERE a.empid = :empid";
$checkStmt = $pdo->prepare($checkQuery);
$checkStmt->bindParam(':empid', $_SESSION['empid'], PDO::PARAM_INT);
$checkStmt->execute();
$attendedEvents = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_FILES['edit_emp_profile'])) {
        $newProfilePicture = !empty($_FILES['edit_emp_profile']['name']);

        if($newProfilePicture) {
            $target_dir = "../../images/profiles/";
            $original_filename = basename($_FILES['edit_emp_profile']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

            $unique_filename = uniqid()."_".time().".".$imageFileType;
            $target_file = $target_dir.$unique_filename;

            $check = getimagesize($_FILES['edit_emp_profile']['tmp_name']);
            if($check === false || $_FILES['edit_emp_profile']['size'] > 500000 || !in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                echo "Sorry, your file was not uploaded.";
            } elseif(move_uploaded_file($_FILES['edit_emp_profile']['tmp_name'], $target_file)) {
                $filename = "images/profiles/".basename($target_file);
                $query = $pdo->prepare("UPDATE tbempaccount SET emp_profile = :emp_profile WHERE empid = :empid");
                if($query->execute([':emp_profile' => $filename, ':empid' => $_SESSION['empid']])) {
                    header("Location: myprofile.php");
                    exit();
                } else {
                    echo "Error updating profile picture.";
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } elseif(isset($_POST['cancel'])) {
        $deleteQuery = "DELETE FROM tb_attendees WHERE event_id = :event_id AND empid = :empid";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':event_id', $_POST['event_id'], PDO::PARAM_INT);
        $deleteStmt->bindParam(':empid', $_SESSION['empid'], PDO::PARAM_INT);
        $deleteStmt->execute();

        $_SESSION['delete_action_completed'] = true;

        header("Location: myprofile.php");
        exit;
    }
}

try {
    $departmentId = $_SESSION['department_id'];

    $query = $conn->prepare("SELECT e.event_id, e.event_title, e.event_detail, e.event_date, e.status, e.header_image, d.department_name FROM tb_event e
                            INNER JOIN tb_department d ON e.department_id = d.department_id
                            WHERE e.department_id = ?");
    $query->bind_param('i', $departmentId);

    if(!$query->execute()) {
        throw new Exception("Query failed: ".$query->error);
    }

    $result = $query->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $ex) {
    echo "Error: ".$ex->getMessage();
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
    <?php include '../../components/facultyHeader.php'; ?>

    <main>
        <div class="profile-sec">
            <?php
            if(isset($profileData)) {
                ?>
                <div class="profile-header">
                </div>

                <div class="profile-info">
                    <?php
                    if($profileData['emp_profile']) {
                        ?>
                        <div class="profile-image">
                            <div class="profile-overlay" id="profileOverlay">
                                <p>Change Profile</p>
                            </div>
                            <img src="../../<?php echo $profileData['emp_profile']; ?>" alt="Profile Image" id="profileImage">
                        </div>
                        <?php
                    } else {
                        ?>
                        <p class="profile-image">No image available</p>
                        <?php
                    }
                    ?>

                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="file" name="edit_emp_profile" id="edit_emp_profile" accept="image/*"
                            style="display: none;">
                        <button type="submit" name="submit_profile" style="display: none;"></button>
                    </form>

                    <div class="info">
                        <div class="studinfo">
                            <p id="username">
                                <?php echo $profileData['firstname'].' '.$profileData['lastname']; ?>
                            </p>
                            <p class="stud">Teacher at Batangas State University</p>
                            <p id="dept">
                                Department
                                <?php echo $profileData['department_name']; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="child-box" id="profileContainer">
                </div>

                <form method="post" action="" enctype="multipart/form-data">
                    <input type="file" name="edit_emp_profile" id="edit_emp_profile" accept="image/*"
                        style="display: none;">
                    <button type="submit" name="submit_profile" style="display: none;"></button>
                </form>
                <?php
            }
            ?>
        </div>

        <h1 class="events-h">My Events</h1>
        <div class="box-container">
            <?php
            if(count($attendedEvents) === 0) {
                echo '<p>No events attended.</p>';
            } else {
                foreach($attendedEvents as $attendedEvent) {
                    $event_id = $attendedEvent["event_id"];
                    $event_title = $attendedEvent["event_title"];
                    $event_detail = $attendedEvent["event_detail"];
                    $event_date = date('F j, Y', strtotime($attendedEvent["event_date"]));
                    $status = $attendedEvent["status"];
                    $header_image = $attendedEvent["header_image"];
                    $department_name = $attendedEvent["department_name"];

                    $attendees_query = $conn->prepare("SELECT COUNT(*) as attendee_count FROM tb_attendees WHERE event_id = ?");
                    $attendees_query->bind_param('i', $event_id);
                    $attendees_query->execute();
                    $attendees_row = $attendees_query->get_result()->fetch_assoc();
                    $attendee_count = $attendees_row['attendee_count'];

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
                                if($statusClass == 'upcoming-event' || $statusClass == 'ongoing-event') {
                                    echo '<button type="button" onclick="openCancelModal('.$event_id.')" id="cancelbtn">Cancel</button>';
                                }
                                ?>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

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
            document.addEventListener('DOMContentLoaded', function () {
                var profileImage = document.getElementById('profileOverlay');
                var fileInput = document.getElementById('edit_emp_profile');

                profileImage.addEventListener('click', function () {
                    fileInput.click();
                });

                fileInput.addEventListener('change', function () {
                    document.querySelector('form').submit();
                });
            });

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