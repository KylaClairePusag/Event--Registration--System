<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Resume the existing session
include '../../config/config.php';
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

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query to get admin profile
    $sql = "SELECT admin_profile FROM tb_admin WHERE admin_email = :admin_email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":admin_email", $_SESSION['admin_email']);
    $stmt->execute();
    $adminProfile = $stmt->fetchColumn();
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
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class="active"' : ''; ?>>
                        <a href="settings.php">Settings</a>
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