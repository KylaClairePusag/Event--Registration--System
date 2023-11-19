<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start(); // Resume the existing session

// Check if the admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['email']) || !isset($_SESSION['email'])) {
    header("Location: ./signin.php");
    exit();
}

$adminName = $_SESSION['email'];


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        @font-face {
            font-family: 'Geist';
            src: url('Geist/Geist-Regular.woff2') format('woff2'),
                url('Geist/Geist-Regular.woff') format('woff'),
                url('Geist/Geist-Regular.otf') format('opentype');
            font-weight: 400;
            /* Normal weight */
            font-style: normal;
        }

        @font-face {
            font-family: 'Geist';
            src: url('Geist/Geist-Medium.woff2') format('woff2'),
                url('Geist/Geist-Medium.woff') format('woff'),
                url('Geist/Geist-Medium.otf') format('opentype');
            font-weight: 500;
            /* Medium weight */
            font-style: normal;
        }

        @font-face {
            font-family: 'Geist';
            src: url('Geist/Geist-SemiBold.woff2') format('woff2'),
                url('Geist/Geist-SemiBold.woff') format('woff'),
                url('Geist/Geist-SemiBold.otf') format('opentype');
            font-weight: 600;
            /* SemiBold weight */
            font-style: normal;
        }

        @font-face {
            font-family: 'Geist';
            src: url('Geist/Geist-Bold.woff2') format('woff2'),
                url('Geist/Geist-Bold.woff') format('woff'),
                url('Geist/Geist-Bold.otf') format('opentype');
            font-weight: 700;
            /* Bold weight */
            font-style: normal;
        }

        /* Repeat the pattern for other font weights and styles */


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font: 16px/1.6 'Geist', sans-serif;

        }

        header {
            border-bottom: 1px solid #e4e4e7;

        }

        nav,
        main {
            max-width: 1500px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 2rem;
        }

        nav {
            height: 4rem;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;

        }

        nav h3 {
            font-size: 1.2rem;
            letter-spacing: .1rem;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;

        }

        nav ul li {
            margin: 0 10px;

        }


        nav ul li a {
            text-decoration: none;
            color: #8d8c8d;
            font-size: 1rem;
        }

        header .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            margin-right: 10px;
            border: 1px solid grey;
            border-radius: 20px;

        }

        .profile span {
            font-weight: bold;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
        }


        /* main */

        main {
            padding: 1.3rem 2rem 2rem;
        }

        .title h1 {
            font-size: 1.8rem;
            margin-bottom: 1.4rem;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        .card-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            margin-bottom: .4rem;
        }

        .card img {
            width: 2rem;
        }

        .card h2 {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }

        .card p {
            font-size: 1.5rem;
            font-weight: 700;
            margin: .5rem 0;
        }

        .card span {
            font-size: .9rem;
            font-weight: 500;
        }


        .card .increase {
            color: #4CAF50;
            font-weight: bold;
        }

        .card .decrease {
            color: #F44336;
            font-weight: bold;
        }

        #rso-section {
            display: none;
        }
    </style>
</head>

<body>

    <header>
        <nav>
            <div class="logo">
        
                <ul>
                
                    <li><a href="event_faculty.php">Events</a></li>
                    <li><a href="#">Profile</a></li>
                </ul>
            </div>
            <div class="profile">
                <img src="avatar1.jpg" alt="Alicia Koch" width="40" height="40">
                <span>
                    <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </span>
                <a href="logout.php">Logout</a>

            </div>
        </nav>


    </header>



  




</body>

</html>