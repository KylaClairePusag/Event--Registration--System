<?php
include '../../config/config.php';
session_destroy(); // Destroy the session
header("Location: signin.php"); // Redirect to login page
exit();
