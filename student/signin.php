<?php


if(isset($_SESSION['student_email'])) {
    header("Location: ./home");
    exit;
}

include '../config/config.php';


$error = null;

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $student_email = filter_input(INPUT_POST, 'student_email', FILTER_SANITIZE_EMAIL);
    $student_password = filter_input(INPUT_POST, 'student_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(empty($student_email) || empty($student_password)) {
        $error = "*** Please fill in all fields. ***";
    } else {
        try {
            $query = "SELECT * FROM tbstudentaccount WHERE student_email = :student_email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':student_email', $student_email, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row && $row['student_password'] === $student_password) {
                $_SESSION['student_email'] = $row['student_email'];
                $_SESSION['student_name'] = $row['firstname'].' '.$row['lastname'];
                $_SESSION['department_id'] = $row['department_id'];
                $_SESSION['student_id'] = $row['studid'];
                header("Location: ./home");
                exit;
            } else {
                $error = "*** Invalid Login Credentials. ***";
            }
        } catch (PDOException $e) {
            $error = "Failed to prepare the statement: ".$e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="../styles/auth.css">
</head>

<body>
    <main>
        <section class="first-section">
            <div class="header">
                <h1 id="redirectLink">Event Registration</h1>
            </div>
            <div class="box">
                <div class="login-container">
                    <h1>Student Login</h1>
                    <p>Please enter your credentials to log in.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="student_email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="student_password" placeholder="Password" required>
                        </div>
                        <div class="error<?php if(!empty($error))
                            echo ' show'; ?>">
                            <?php if(!empty($error)): ?>
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
                    <h3>Welcome to the university portal</h3>
                    <span>Explore Batangas State University, your gateway to education and innovation.</span>
                </div>
            </div>
        </section>
    </main>
    <script>
        document.getElementById("redirectLink").addEventListener("click", function () {
            // Redirect to the ./index page
            window.location.href = "../";
        });
    </script>
</body>

</html>