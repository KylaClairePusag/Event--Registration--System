<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/dashboard.module.css">
</head>

<body>
    <nav>
        <div class="logo">
            <h3>Event</h3>
            <ul>
                <li><a href="#overview">Overview</a></li>
                <li><a href="#rso">RSO</a></li>
                <li><a href="#admin">Admin</a></li>
                <li><a href="#reports">Reports</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <section id="overview">Overview</section>
        <section id="rso">
            <?php require_once 'rso.php' ?>
        </section>
        <section id="admin">
            <?php require_once 'admin.php' ?>
        </section>
        <section id="reports">Reports</section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            showSection(getCurrentSectionFromUrl());

            document.querySelectorAll('nav a').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const sectionId = this.getAttribute('href').substring(1);
                    window.location.hash = sectionId;
                    showSection(sectionId);
                });
            });

            window.addEventListener('hashchange', function () {
                showSection(getCurrentSectionFromUrl());
            });
        });

        function getCurrentSectionFromUrl() {
            return window.location.hash.substring(1);
        }

        function showSection(sectionId) {
            document.querySelectorAll('section').forEach(function (section) {
                section.style.display = 'none';
            });

            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
</body>

</html>