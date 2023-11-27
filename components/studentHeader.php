<?php
include '../../config/config.php';

// Check if the student is logged in, otherwise redirect to the login page
if(!isset($_SESSION['student_email'])) {
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
    <style>
        h3 a {

            text-decoration: none;
            color: black;
            font-weight: 700;
        }

        /* Add your dropdown styles here */
        h5 {
            padding: 12px 16px;

            font-weight: 300;
            border-bottom: 1px solid #f1f1f1;

        }

        .dropdown {
            position: relative;
            display: inline-block;
            border-radius: 20px;

        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 20px;
        }

        .dropdown-content a {
            color: black;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Style for the profile image */
        .profile-img {
            max-width: 50px;
            /* Adjust the size as needed */
            border-radius: 50%;
            cursor: pointer;
        }

        /* Style for the profile dropdown */
        .profile-dropdown {
            border-radius: 10px;
            position: absolute;
            top: 100%;
            right: 0;
            display: none;
            background-color: white;
            box-shadow: rgba(17, 17, 26, 0.05) 0px 4px 16px, rgba(17, 17, 26, 0.05) 0px 8px 32px;
            z-index: 1;
            width: 200px;
            text-align: left;

        }

        .profile-dropdown a {
            color: black;
            text-decoration: none;
            display: block;
            font-weight: 300;
            font-size: .9rem;
            padding: 12px 16px;



        }

        .profile-dropdown a:hover {
            background-color: #f1f1f1;
        }
    </style>
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
                    <!-- Add profile dropdown items as needed -->
                    <h5>
                        <?php echo htmlspecialchars($student_email); ?>
                    </h5>

                    <a href="myprofile.php">Profile</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>

        </nav>
        <script>
            function toggleProfileDropdown() {
                var dropdown = document.getElementById("profileDropdown");
                dropdown.style.display = (dropdown.style.display === 'block' || dropdown.style.display === '') ? 'none' : 'block';
            }

            // Add event listener to close dropdown when clicking outside
            document.addEventListener('click', function (event) {
                var dropdown = document.getElementById("profileDropdown");
                var profileImg = document.querySelector('.profile-img');

                // Check if the clicked element is inside the profile dropdown or the profile image
                if (!dropdown.contains(event.target) && !profileImg.contains(event.target)) {
                    // If outside, close the dropdown
                    dropdown.style.display = 'none';
                }
            });
        </script>
    </header>
</body>

</html>