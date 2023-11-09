<?php
session_start(); // Resume the existing session
session_destroy(); // Destroy the session
header("Location: signin.php"); // Redirect to login page
exit();
