<?php
// Database connection setup
include '../../config/config.php';
// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

include '../../components/rsoHeader.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>

    <div class="Tableheader">
    </div>
    <div class="insertion">
        <div class="container">
            <div class="reports boxes">
                <div class="box">
                    <h3>Attendees List</h3>
                    <a href="reports_eventList.php"><button>View Report</button></a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>