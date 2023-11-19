<?php
include '../../config/config.php';
include '../../components/rsoHeader.php';

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');

$eventId = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if (!$eventId) {
    echo "Error: Event ID not provided.";
    die();
}

$sql = "SELECT * FROM tb_attendees WHERE event_id = $eventId";
$result = $conn->query($sql);

if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    $eventNameQuery = $conn->query("SELECT event_title FROM tb_event WHERE event_id = $eventId");
    $eventName = $eventNameQuery->fetch_assoc();
    $event_title = $eventName ? $eventName["event_title"] : '';
    
} else {
    echo "Error executing query: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendees Table</title>
    <link rel="stylesheet" href="../../styles/rso.css">
    <style>
    h2 {
        text-align: center;
    }
    </style>
</head>

<body onload="window.print()">


    <h2><?php echo $event_title ?> Attendees List</h2> <br>

    <section class="tableContainer">
        <?php
include '../../components/table.component.php';

$head = array('', 'Name', 'Course');
$body = array();

$count = 1;

foreach ($rows as $row) {
    $event_id = $row["event_id"];
    $student_id = $row["student_id"];

    $studentNameQuery = $conn->query("SELECT CONCAT(firstname, ' ', lastname) AS student_name FROM tbstudinfo WHERE studid = $student_id");
    $studentName = $studentNameQuery->fetch_assoc();
    $student_name = $studentName ? $studentName["student_name"] : '';

    $courseNameQuery = $conn->query("SELECT course FROM tbstudinfo WHERE studid = $student_id");
    $courseName = $courseNameQuery->fetch_assoc();
    $course = $courseName ? $courseName["course"] : '';

    $body[] = array($count++, $student_name, $course);
}

createTable($head, $body);
?>

    </section>

</body>

</html>