<?php
// includes/helpers.php — Shared utility functions
// CTEC2712N — Musanna Khandakar

// e() — Escape output to prevent XSS.
// Wrap EVERY database value you print in e().
// Example: echo e($programme['ProgrammeName']);
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// redirect() — Safely redirect to another page and stop execution.
// Example: redirect('/student-course-hub/student/index.php');
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}
