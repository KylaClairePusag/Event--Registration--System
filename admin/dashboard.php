<?php
session_start(); // Resume the existing session

// Check if the admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['email']) || !isset($_SESSION['admin_name'])) {
    header("Location: ./login.php");
    exit();
}

// Greet the admin
$adminName = $_SESSION['admin_name']; // Assuming you have stored admin name in session during login
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
            src: url('fonts/Geist/Geist-Regular.woff2') format('woff2'),
                url('fonts/Geist/Geist-Regular.woff') format('woff'),
                url('fonts/Geist/Geist-Regular.otf') format('opentype');
            font-weight: 400;
            /* Normal weight */
            font-style: normal;
        }

        @font-face {
            font-family: 'Geist';
            src: url('fonts/Geist/Geist-Medium.woff2') format('woff2'),
                url('fonts/Geist/Geist-Medium.woff') format('woff'),
                url('fonts/Geist/Geist-Medium.otf') format('opentype');
            font-weight: 500;
            /* Medium weight */
            font-style: normal;
        }

        @font-face {
            font-family: 'Geist';
            src: url('fonts/Geist/Geist-SemiBold.woff2') format('woff2'),
                url('fonts/Geist/Geist-SemiBold.woff') format('woff'),
                url('fonts/Geist/Geist-SemiBold.otf') format('opentype');
            font-weight: 600;
            /* SemiBold weight */
            font-style: normal;
        }

        @font-face {
            font-family: 'Geist';
            src: url('fonts/Geist/Geist-Bold.woff2') format('woff2'),
                url('fonts/Geist/Geist-Bold.woff') format('woff'),
                url('fonts/Geist/Geist-Bold.otf') format('opentype');
            font-weight: 700;
            /* Bold weight */
            font-style: normal;
        }


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

        .active-link {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <?php
    // Assuming the URL is something like 'http://localhost/Event--Registration--System/admin/#overview.php'
    $current_section = basename($_SERVER['REQUEST_URI'], ".php");

    // Function to determine if the link is active
    function is_active($section)
    {
        global $current_section;
        return strpos($current_section, $section) !== false ? 'active-link' : '';
    }
    ?>


    <header>
        <nav>
            <div class="logo">
                <h3>Event</h3>
                <ul>
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="#rso">Rso</a></li>
                    <li><a href="#admin">Admin</a></li>
                    <li><a href="#settings">Settings</a></li>
                </ul>
            </div>
            <div class="profile">
                <img src="avatar1.jpg" alt="Alicia Koch" width="40" height="40">
                <span>
                    <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </span>
            </div>
        </nav>


    </header>



    <main class="dashboard-grid">
        <div class="title">
            <h1>Dashboard</h1>

            <section class="dashboard">
                <div class="card">
                    <div class="card-title">
                        <h2>Total Events</h2>
                        <img src="../images/eventIcon.png" alt="" srcset="">
                    </div>
                    <p>+4,123</p>
                    <span class="percentage">+20.1% from last month</span>
                </div>
                <div class="card">
                    <div class="card-title">
                        <h2>Students</h2>
                        <img src="../images/studentIcon.png" alt="" srcset="">
                    </div>
                    <p>+2350</p>
                    <span class="percentage">+20.1% from last month</span>
                </div>
                <div class="card">
                    <div class="card-title">
                        <h2>Faculty</h2>
                        <img src="../images/teacherIcon.png" alt="" srcset="">
                    </div>
                    <p>+12,234</p>
                    <span class="percentage">+20.1% from last month</span>
                </div>
                <div class="card">
                    <div class="card-title">
                        <h2>Admin</h2>
                        <img src="../images/adminIcon.png" alt="" srcset="">
                    </div>
                    <p>+573</p>
                    <span class="percentage">+20.1% from last month</span>
                </div>
            </section>
        </div>

    </main>
    <script>
        // JavaScript to add 'active' class to the current page in the sidebar
        document.addEventListener('DOMContentLoaded', (event) => {
            const currentLocation = window.location.pathname.split('/').pop();
            const menuItems = document.querySelectorAll('#sidebar a');
            menuItems.forEach(item => {
                if (item.getAttribute('href') === currentLocation) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>