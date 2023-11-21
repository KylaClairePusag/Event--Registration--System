<?php
include '../../config/config.php';

// Check if the student is logged in, otherwise redirect to the login page
if (!isset($_SESSION['student_email'])) {
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
    $sql = "SELECT firstname, lastname, student_profile,student_email FROM tbstudinfo WHERE student_email = :student_email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":student_email", $_SESSION['student_email']);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
    $student_email = $studentData['student_email'];
    $studentFirstName = $studentData['firstname'];
    $studentLastName = $studentData['lastname'];
    $studentProfile = $studentData['student_profile'];
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
    <title>Student Dashboard</title>
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
                    <!-- Add other student-specific menu items as needed -->
                </ul>
            </div>
            <div class="profile">
                <?php echo htmlspecialchars($student_email); ?>
                <?php echo '<img src="../../' . $studentProfile . '" alt="Student Profile Image" class="profile"'; ?>
            </div>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
</body>

</html>