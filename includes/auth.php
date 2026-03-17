<?php
// includes/auth.php — Admin session protection
// CTEC2712N — Musanna Khandakar

// Add these two lines at the TOP of every admin page:
// require_once '../includes/auth.php';
// requireAdmin();

function requireAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) {
        // Not logged in — redirect to login page
        header('Location: /student-course-hub/auth/login.php');
        exit;
    }
}
