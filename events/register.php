<?php
$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if (isset($_POST['attend_event'])) {
    $attendee= $_POST['attendee_id'];
    $event= $_SESSION['event_id'];
    $student = $_POST['student_id'];
    $faculty = $_POST['faculty_id'];
    $rso = $_POST['rso_id'];

    $stmt = $conn->prepare('INSERT INTO tb_attendees (attendee_id, event_id,student_id,faculty_id,rso_id) VALUES (?, ?)');
    $stmt->bind_param('iiiii', $attendee, $event, $student, $faculty, $rso);
    
    if ($stmt->execute()) {
        echo "<script>alert('Success!');</script>";
    } else {
        echo "<script>alert('Please try again.');</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request. Please try again.');</script>";
}

$conn->close();
header("location:event.php");
?>