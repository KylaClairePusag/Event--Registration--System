<?php
// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

try {
    
    $query = $pdo->prepare("SELECT * FROM tb_event");
    if (!$query->execute()) {
        throw new Exception("Query failed: " . implode(" ", $query->errorInfo()));
    }

    // Fetch results
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

    <section class="tableContainer">
        <?php include '../../components/table.component.php';

            $head = array('Events', 'Action');
            $body = array();

            foreach ($rows as $row) {
                $event_id = $row["event_id"];
                $event_title = $row["event_title"];

                $viewAttendeesButton = '<a href="attendeesList.php?event_id=' . $event_id . '"><button>Print Report</button></a>';

                $body[] = array($event_title, $viewAttendeesButton);
            }

            createTable($head, $body);
            ?>
    </section>

    <?php

    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
    const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
    <script src="../../script/event.js"></script>
</body>

</html>