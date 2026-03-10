<?php
// includes/helpers.php — Utility functions used across the whole project

// e() — Escape output to prevent XSS. Use on EVERY database value you print.
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// redirect() — Safely redirect to another page
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}
