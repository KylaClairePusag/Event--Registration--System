<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../../config/config.php';

if(!isset($_SESSION['emp_email'])) {
    header("Location: ../signin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_ba3101";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlEmpAccount = "SELECT empid, emp_profile, emp_email FROM tbempaccount WHERE emp_email = :emp_email";
    $stmtEmpAccount = $pdo->prepare($sqlEmpAccount);
    $stmtEmpAccount->bindParam(":emp_email", $_SESSION['emp_email']);
    $stmtEmpAccount->execute();
    $empData = $stmtEmpAccount->fetch(PDO::FETCH_ASSOC);
    $emp_id = $empData['empid'];
    $emp_email = $empData['emp_email'];
    $empProfile = $empData['emp_profile'];

    $sqlEmpInfo = "SELECT * FROM tbempinfo WHERE empid = :empid";
    $stmtEmpInfo = $pdo->prepare($sqlEmpInfo);
    $stmtEmpInfo->bindParam(":empid", $emp_id);
    $stmtEmpInfo->execute();
    $empInfo = $stmtEmpInfo->fetch(PDO::FETCH_ASSOC);
    $lastname = $empInfo['lastname'];
    $firstname = $empInfo['firstname'];
    $department = $empInfo['department'];

    $sqlStudInfo = "SELECT * FROM tbstudinfo";
    $stmtStudInfo = $pdo->prepare($sqlStudInfo);
    $stmtStudInfo->execute();
    $studData = $stmtStudInfo->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: ".$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/header.css" type="text/css">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <h3><a href="./">Event</a></h3>

            </div>
            <div class="profile dropdown">
                <span class="profile-email">
                    <?= htmlspecialchars($firstname); ?>
                    <?= htmlspecialchars($lastname); ?>

                </span>
                <div class="profile">
                    <?php
                    if(!empty($empProfile)) {
                        echo '<img src="../../'.$empProfile.'" alt="emp Profile Image" onclick="toggleProfileDropdown()" class="profile-img" >';
                    } else {
                        echo '<img src="../../images/alt.png" alt="emp Profile Image" onclick="toggleProfileDropdown()" class="profile-img" >';
                    }
                    ?>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <h5>
                        <?= htmlspecialchars($emp_email); ?>
                    </h5>
                    <a href="myprofile.php">Profile</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <script>
        // Profile Dropdown
        function toggleProfileDropdown() {
            var dropdown = document.getElementById("profileDropdown");
            dropdown.style.display =
                dropdown.style.display === "block" || dropdown.style.display === ""
                    ? "none"
                    : "block";
        }

        document.addEventListener("click", function (event) {
            var dropdown = document.getElementById("profileDropdown");
            var profileImg = document.querySelector(".profile-img");

            if (!dropdown.contains(event.target) && !profileImg.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });

    </script>
</body>

</html>