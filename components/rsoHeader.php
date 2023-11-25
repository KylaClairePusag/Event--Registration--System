<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../../config/config.php';

// Check if the RSO is logged in, otherwise redirect to login page
if (!isset($_SESSION['rso_email']) || !isset($_SESSION['rso_name'])) {
    header("Location: ../signin.php");
    exit();
}

$rsoName = $_SESSION['rso_name'];

// Prepare and execute the query to get admin profile
try {
    $sql = "SELECT rso_profile FROM tb_rso WHERE rso_email = :rso_email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":rso_email", $_SESSION['rso_email']);
    $stmt->execute();
    $rsoProfile = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
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
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>
                        <a href="./">Overview</a>
                    </li>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'class="active"' : ''; ?>>
                        <a href="events.php">Events</a>
                    </li>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'attendees.php' ? 'class="active"' : ''; ?>>
                        <a href="attendees.php">Attendees</a>
                    </li>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'class="active"' : ''; ?>>
                        <a href="reports.php">Reports</a>
                    </li>
                </ul>
            </div>
            <div class="profile">
                <?php echo htmlspecialchars($rsoName); ?>
                <?php echo '<img src="../../' . $rsoProfile . '" alt="RSO Profile Image" class="profile"'; ?>
                </span>
                <a href="../logout.php">Logout</a>
            </div>
        </nav>
    </header>
</body>

</html>