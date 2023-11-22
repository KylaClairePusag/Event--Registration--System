<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../../config/config.php';

// Check if the student is logged in, otherwise redirect to the login page
if (!isset($_SESSION['faculty_email'])) {
    header("Location: ../signin.php");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_ba3101";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query to get student profile
    $sql = "SELECT faculty_name, faculty_position, faculty_profile,faculty_email FROM tb_faculty WHERE faculty_email = :faculty_email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":faculty_email", $_SESSION['faculty_email']);
    $stmt->execute();
    $facultyData = $stmt->fetch(PDO::FETCH_ASSOC);
    $faculty_email = $facultyData['faculty_email'];
    $facultyName = $facultyData['faculty_name'];
    $facultyPosition = $facultyData['faculty_position'];
    $facultyProfile = $facultyData['faculty_profile'];
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/header.css" type="text/css">
    <title>Faculty Dashboard</title>
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
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class="active"' : ''; ?>>
                        <a href="settings.php">Settings</a>
                    </li>
                    <!-- Add other student-specific menu items as needed -->
                </ul>
            </div>
            <div class="profile">
                <?php echo htmlspecialchars($faculty_email); ?>
                <?php echo '<img src="../../' . $facultyProfile . '" alt="Faculty Profile Image" class="profile"'; ?>
            </div>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
</body>

</html>
