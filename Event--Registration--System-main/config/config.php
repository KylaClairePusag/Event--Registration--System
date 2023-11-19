<?php
// Connect to the database
$host = "localhost"; // Your database host
$user = "root"; // Your database user
$password = ""; // Your database password
$dbname = "db_ba3101"; // Your database name

// Connect to the database
$conn = new mysqli($host, $user, $password, $dbname);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

