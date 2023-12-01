<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../../config/config.php';

if(!isset($_SESSION['rso_email']) || !isset($_SESSION['rso_name'])) {
    header("Location: ../signin.php");
    exit();
}

try {
    $sql = "SELECT rso_name, rso_email, rso_profile FROM tb_rso WHERE rso_email = :rso_email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":rso_email", $_SESSION['rso_email']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $rsoName = $result['rso_name'];
    $rsoEmail = $result['rso_email'];
    $rsoProfile = $result['rso_profile'];
} catch (PDOException $e) {
    die("Connection failed: ".$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <title>Dashboard</title>
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <h3>Event</h3>
                <ul>
                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>
                        <a href="./">Events</a>
                    </li>

                    <li <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'class="active"' : ''; ?>>
                        <a href="reports.php">Reports</a>
                    </li>
                </ul>
            </div>

            <div class="profile dropdown">
                <span class="profile-email">
                    <?= htmlspecialchars($rsoName); ?>

                </span>
                <div class="profile">
                    <?php
                    if(!empty($rsoProfile)) {
                        echo '<img src="../../'.$rsoProfile.'" alt="RSO Profile Image" onclick="toggleProfileDropdown()" class="profile-img" >';
                    } else {
                        echo '<img src="../../images/alt.png" alt="RSO Profile Image" onclick="toggleProfileDropdown()" class="profile-img" >';
                    }
                    ?>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <h5>
                        <?= htmlspecialchars($rsoEmail); ?>
                    </h5>

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