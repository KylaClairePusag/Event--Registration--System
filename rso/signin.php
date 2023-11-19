<?php
session_start();
if (isset($_SESSION['rso_email'])) {
    header("Location: ./dashboard");
    exit;
}

include '../config/config.php';
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $rso_email = filter_input(INPUT_POST, 'rso_email', FILTER_SANITIZE_EMAIL);
    $rso_password = filter_input(INPUT_POST, 'rso_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($rso_email) || empty($rso_password)) {
        $error = "*** Please fill in all fields. ***";
    } else {
        try {
            $query = "SELECT * FROM tb_rso WHERE rso_email = :rso_email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':rso_email', $rso_email, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['rso_password'] === $rso_password) {
                $_SESSION['rso_email'] = $row['rso_email'];
                $_SESSION['rso_name'] = $row['rso_name'];

                // Assuming 'department_id' is the name of the column in your tb_rso table
                $_SESSION['department_id'] = $row['department_id'];

                header("Location: ./dashboard");
                exit;
            } else {
                $error = "*** Invalid Login Credentials. ***";
            }
        } catch (PDOException $e) {
            $error = "Failed to prepare the statement: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSO Portal</title>
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
                    <h1>RSO Portal</h1>
                    <p>Please enter your contact details to connect.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="rso_email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
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
                        <h5>By using this service, you understood and agree to the Event Online Registration BSU Terms
                            of Use and Privacy Statement</h5>
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
                    <h3>Welcome to the university portal</h3>
                    <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque enim repudiandae dolorum
                        magnam? Sequi repellendus alias sed nisi tempore.</span>
                </div>
            </div>
        </section>
    </main>
</body>

</html>