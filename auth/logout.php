<?php
// auth/logout.php — Destroys admin session and redirects to login
// CTEC2712N — Musanna Khandakar

session_start();

// Destroy everything in the session
$_SESSION = [];
session_destroy();

// Redirect to login page
header('Location: /student-course-hub/auth/login.php');
exit;
