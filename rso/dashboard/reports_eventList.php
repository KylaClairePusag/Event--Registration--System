<?php
include '../../config/config.php';

$requestUri = $_SERVER['REQUEST_URI'];

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

try {
  
    $userDepartmentId = $_SESSION['department_id'];

    $query = $pdo->prepare("SELECT * FROM tb_event
                            JOIN tb_rso ON tb_event.department_id = tb_rso.department_id
                            WHERE tb_rso.department_id = :userDepartmentId");
    $query->bindParam(':userDepartmentId', $userDepartmentId, PDO::PARAM_INT);

    if (!$query->execute()) {
        throw new Exception("Query failed: " . implode(" ", $query->errorInfo()));
    }

    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>
    <?php include '../../components/rsoHeader.php'; ?>
    <main>
        <section class="tableContainer">
            <?php include '../../components/table.component.php';

            $head = array('Events', 'Action');
            $body = array();

            foreach ($rows as $row) {
                $event_id = $row["event_id"];
                $event_title = $row["event_title"];

          
                $actionCell = '<a href="attendeesList.php?event_id=' . $event_id . '"><button>Student Attendees</button></a>';
                $actionCell .= '<a href="attendeesList_faculty.php?event_id=' . $event_id . '"><button>Faculty Attendees</button></a>';

                $body[] = array($event_title, $actionCell);
            }

            createTable($head, $body);
            ?>
        </section>
    </main>

    <?php

    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
    const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
    <script src="../../script/event.js"></script>
</body>

</html>