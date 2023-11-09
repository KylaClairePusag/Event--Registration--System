<?php
// Start a new session
session_start();
if (isset($_SESSION['email'])) {
    header("Location: ./dashboard.php");
    exit;
}

include '../config/config.php';
$error = null; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $rso_password = filter_input(INPUT_POST, 'rso_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($email) || empty($rso_password)) {
        $error = "*** Please fill in all fields. ***"; // Set the error message
    } else {
        $query = "SELECT * FROM tb_rso WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            // Directly compare the faculty_passwords (constb_facultyer using rso_password hashing in production)
            if ($row && $row['rso_password'] === $rso_password) {
                // Set session variables
                $_SESSION['email'] = $row['email'];
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
    <title>Admin Portal</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <main>
        <section class="first-section">
            <div class="header">

                <h1>Event Registration</h1>
            </div>
            <div class="box">
                <div class="login-container">
                    <h1>Rso Portal</h1>
                    <p>Please enter your contact details to connect.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="rso_password">rso_password</label>
                            <input type="password" name="rso_password" placeholder="Password" required>
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
                        <h5>By using this service, you understood and agree to the
                            Event Online Registration BSU Terms of Use and Privacy
                            Statement
                        </h5>
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