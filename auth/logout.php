<?php
// auth/logout.php — Destroy session and log out
// CTEC2712N — Musanna Khandakar
session_start();

// Clear all session data
$_SESSION = [];

// Destroy the session on the server
session_destroy();

// Send user back to login page
header('Location: /student-course-hub/auth/login.php');
exit;
