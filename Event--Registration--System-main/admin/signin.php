<?php
// Start a new session
session_start();
if (isset($_SESSION['admin_email'])) {
    header("Location: ./dashboard.php");
    exit;
}

include '../config/config.php';
$error = null; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $admin_email = filter_input(INPUT_POST, 'admin_email', FILTER_SANITIZE_EMAIL); // Change variable name to admin_email
    $admin_password = filter_input(INPUT_POST, 'admin_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($admin_email) || empty($admin_password)) {
        $error = "*** Please fill in all fields. ***"; // Set the error message
    } else {
        $query = "SELECT * FROM tb_admin WHERE admin_email = ?"; // Change column name to admin_email
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $admin_email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            // Directly compare the passwords (consider using password hashing in production)
            if ($row && $row['admin_password'] === $admin_password) {
                // Set session variables
                $_SESSION['admin_email'] = $row['admin_email']; // Change session variable to admin_email
                $_SESSION['admin_name'] = $row['admin_name'];
                // Redirect to dashboard
                header("Location: ./dashboard.php");
                exit;
            } else {
                $error = "*** Invalid Login Credentials. ***"; // Set the error message
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <ma
in>
        <section class="first-section">
            <div class="header">
                <h1>Event Registration</h1>
            </div>
            <div class="box">
                <div class="login-container">
                    <h1>Admin Portal</h1>
                    <p>Please enter your contact details to connect.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="admin_email" placeholder="Enter your email" required>

                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="admin_password" placeholder="Password" required>
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