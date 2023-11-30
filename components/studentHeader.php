<?php
include '../../config/config.php';

if(!isset($_SESSION['student_email'])) {
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

    $sql = "SELECT student_profile, student_email FROM tbstudentaccount WHERE student_email = :student_email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":student_email", $_SESSION['student_email']);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
    $student_email = $studentData['student_email'];
    $studentProfile = $studentData['student_profile'];
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
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <h3> <a href="./">Event</a></h3>

            </div>
            <div class="profile dropdown">
                <span class="profile-email">
                    <?php echo htmlspecialchars($student_email); ?>
                </span>
                <img src="../../<?php echo $studentProfile; ?>" alt="Student Profile Image" class="profile-img"
                    onclick="toggleProfileDropdown()" />
                <div class="profile-dropdown" id="profileDropdown">
                    <h5>
                        <?php echo htmlspecialchars($student_email); ?>
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