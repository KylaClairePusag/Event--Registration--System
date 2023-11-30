<?php
include '../../config/config.php';
$requestUri = $_SERVER['REQUEST_URI'];

include '../../components/adminHeader.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="../../styles/rso.css">

    <style>
    .Tableheader {
        padding: 20px 0px;
    }

    .reports .box {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .reports.boxes {
        overflow-x: none;
    }

    .reports button {
        margin-top: 1rem;
        width: 100%;
        max-width: 100px;
        padding: 0.5rem;
        border-radius: 0.5rem;
    }

    .container {
        display: flex;
    }
    </style>
</head>

<body>
    <main>
        <div class="Tableheader">
            <h1>Report</h1>
        </div>
        <br>
        <div class="insertion">
            <div class="container">
                <div class="reports boxes">
                    <div class="box">
                        <img src="../../images/attendees.png" class="icons">
                        <h3>Attendees List</h3>
                        <a href="reports_eventList.php"><button>View Report</button></a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>