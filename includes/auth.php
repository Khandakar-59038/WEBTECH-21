<?php
// includes/auth.php — Session validation. Add to top of every admin page.

function requireAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}
