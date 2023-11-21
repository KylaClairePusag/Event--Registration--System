<?php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
include '../../config/config.php';

if (isset($_SESSION['faculty_email'])) {
    header("Location: ./dashboard.php");
    exit;
}

include '../config/config.php';
$error = null; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $faculty_email = filter_input(INPUT_POST, 'faculty_email', FILTER_SANITIZE_EMAIL);
    $faculty_password = filter_input(INPUT_POST, 'faculty_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($faculty_email) || empty($faculty_password)) {
        $error = "*** Please fill in all fields. ***"; // Set the error message
    } else {
        $query = "SELECT * FROM tb_faculty WHERE faculty_email = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $faculty_email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            // Directly compare the faculty_passwords (constb_facultyer using faculty_password hashing in production)
            if ($row && $row['faculty_password'] === $faculty_password) {
                // Set session variables
                $_SESSION['faculty_email'] = $row['faculty_email'];
                $_SESSION['faculty_name'] = $row['faculty_name'];
                // Redirect to dashboard
                header("Location: ./dashboard.php");
                exit;
            } else {
                $error = "*** Invalid Faculty Login Credentials. ***"; // Set the error message
            }
        } else {
            $error = "Failed to prepare the statement: " . mysqli_error($conn); // Set the error message
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="wtb_facultyth=device-wtb_facultyth, initial-scale=1.0">
    <title>Faculty Portal</title>
    <link rel="stylesheet" href="../styles/auth.css">
</head>

<body>
    <main>
        <section class="first-section">
            <div class="header">

                <h1>Event Registration</h1>
            </div>
            <div class="box">
                <div class="login-container">
                    <h1>Faculty Portal</h1>
                    <p>Please enter your contact details to connect.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="faculty_email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="faculty_password">Password</label>
                            <input type="password" name="faculty_password" placeholder="Password" required>
                        </div>
                        <div class="error<?php if (!empty($error))
                            echo ' show'; ?>">
                            <?php if (!empty($error)): ?>
                                <p>
                                    <?php echo $error; ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <button type="submit" name="submit">Sign in</button>
                    </form>
                </div>

            </div>

        </section>
        <section class="second-section">
            <div class="yellow"></div>
            <div class="container">

                <div class="title">
                    <h1>BATANGAS STATE UNIVERSITY</h1>
                    <h3>The National Engineering University</h3>
                    <img src="../images/Batangas_State_Logo.png" alt="Batangas State University Logo">
                </div>
                <div class="welcome-context">
                    <h3>
                        Welcome to the university portal
                    </h3>
                    <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque enim repudiandae dolorum
                        magnam?
                        Sequi repellendus alias sed nisi tempore.</span>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
