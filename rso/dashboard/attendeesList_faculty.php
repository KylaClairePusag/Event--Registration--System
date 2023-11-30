<?php
include '../../config/config.php';

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
    <title>Attendees List</title>
    <link rel="stylesheet" href="../../styles/rso.css">
    <style>
    h2 {
        text-align: center;
    }
    </style>
</head>

<body onload="window.print()">

    <h2><?php echo $event_title ?> Faculty Attendees List</h2> <br>

    <section class="tableContainer">
        <?php
        include '../../components/table.component.php';

        $head = array('', 'Name', 'Department');
        $body = array();

        $count = 1;

        foreach ($rows as $row) {
            $employeeNameQuery = $conn->prepare("SELECT CONCAT(firstname, ' ', lastname) AS employee_name, department FROM tbempinfo WHERE empid = ?");
            $employeeNameQuery->bind_param("i", $empid);
            $empid = $row["empid"];
            $employeeNameQuery->execute();
            $employeeNameResult = $employeeNameQuery->get_result();
            $employeeData = $employeeNameResult->fetch_assoc();
            $employee_name = $employeeData ? $employeeData["employee_name"] : '';
            $department = $employeeData ? $employeeData["department"] : '';
            $employeeNameQuery->close();

            $body[] = array($count++, $employee_name, $department);
        }

        createTable($head, $body);
        ?>

    </section>

</body>

</html>