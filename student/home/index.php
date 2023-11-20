<?php
// Database connection setup
include '../../config/config.php';
session_start();

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

try {
    $query = $conn->prepare("SELECT event_id, event_title, event_detail, event_date, status, header_image FROM tb_event WHERE department_id = ?");
    $query->bind_param('i', $departmentId);

    $departmentId = $_SESSION['department_id'];
    if (!$query->execute()) {
        throw new Exception("Query failed: " . $query->error);
    }

    // Fetch results
    $result = $query->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
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
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <div class="box-container">
            <?php
            foreach ($rows as $row) {
                $event_id = $row["event_id"];
                $event_title = $row["event_title"];
                $event_detail = $row["event_detail"];
                $event_date = $row["event_date"];
                $status = $row["status"];
                $header_image = $row["header_image"];
                ?>
                <div class="event-box">
                    <div class="header">
                        <img src="../../<?php echo $header_image; ?>" alt="Event Image">

                    </div>
                    <div class="event-content">
                        <h2>
                            <?php echo $event_title; ?>
                        </h2>
                        <p id="details">
                            <?php echo $event_detail; ?>
                        </p>
                        <p>Date:
                            <?php echo $event_date; ?>
                        </p>
                        <p>Status:
                            <?php echo $status; ?>
                        </p>
                    </div>
                </div>
            <?php } ?>

        </div>
    </main>
</body>

</html>