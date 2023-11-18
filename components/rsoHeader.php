<?php
session_start(); // Resume the existing session

// Check if the admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['rso_email']) || !isset($_SESSION['rso_name'])) {
    header("Location: ../signin.php");
    exit();
}

$adminName = $_SESSION['rso_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/header.css">
    <title>Dashboard</title>
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <h3>Event</h3>
                <ul>
                    <li><a href="./">Overview</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="attendees.php">Attendees</a></li>
                    <li><a href="reports.php">Reports</a></li>

                </ul>
            </div>
            <div class="profile">

                <?php echo htmlspecialchars($_SESSION['rso_name']); ?>
                </span>
                <a href="../logout.php">Logout</a>
            </div>
        </nav>
    </header>