<?php
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/Event--Registration--System/admin/':
        // Render the student dashboard page
        require __DIR__ . '/views/dashboard.php';
        break;
    case '/Event--Registration--System/admin/rso':
        // Render the RSO page
        require __DIR__ . '/views/rso.php';
        break;
    default:
        // Handle 404 error or redirect to the main page
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}
