<?php
// includes/header.php — Shared HTML header for all student pages
// CTEC2712N — Musanna Khandakar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Student Course Hub' ?></title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/student.css">
</head>
<body>

<!-- Skip to main content link for keyboard users (accessibility) -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<header class="site-header">
    <div class="header-inner">
        <a href="/student-course-hub/student/index.php" class="site-logo">
            Student Course Hub
        </a>
        <nav aria-label="Main navigation">
            <ul class="nav-list">
                <li><a href="/student-course-hub/student/index.php">Programmes</a></li>
            </ul>
        </nav>
    </div>
</header>

<main id="main-content">