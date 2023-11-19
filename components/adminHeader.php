<?php
session_start(); // Resume the existing session

// Check if the admin is logged in, otherwise redirect to the login page
if (!isset($_SESSION['admin_email']) || !isset($_SESSION['admin_name'])) {
    header("Location: ../signin.php");
    exit();
}

$adminName = $_SESSION['admin_name'];

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_ba3101";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the query to get admin profile
$sql = "SELECT admin_profile FROM tb_admin WHERE admin_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['admin_email']);
$stmt->execute();
$stmt->bind_result($adminProfile);
$stmt->fetch();
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/header.css" type="text/css">
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
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'rso.php' ? 'class="active"' : ''; ?>>
                        <a href="rso.php">RSO</a>
                    </li>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'class="active"' : ''; ?>>
                        <a href="admin.php">Admin</a>
                    </li>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'class="active"' : ''; ?>>
                        <a href="reports.php">Reports</a>
                    </li>
                </ul>
            </div>
            <div class="profile">
                <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                <?php echo '<img src="../../' . $adminProfile . '" alt="Admin Profile Image" class="profile"'; ?>

            </div>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
</body>

</html>