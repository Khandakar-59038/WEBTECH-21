<?php //admin/index.php


// admin/index.php — Admin dashboard with live statistics
// CTEC2712N — Musanna Khandakar
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Run 4 queries to get the live stats shown on the dashboard cards
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM Programmes');
$stmt->execute();
$totalProgrammes = $stmt->fetch()['total'];

$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM Programmes WHERE IsPublished = 1');
$stmt->execute();
$publishedProgrammes = $stmt->fetch()['total'];

$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM InterestedStudents WHERE IsActive = 1');
$stmt->execute();
$totalStudents = $stmt->fetch()['total'];

$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM Modules');
$stmt->execute();
$totalModules = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Student Course Hub Admin</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/admin.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<header class="site-header">
    <div class="header-inner">
        <a href="/student-course-hub/admin/index.php" class="site-logo">SCH Admin Panel</a>
        <nav aria-label="Admin navigation">
            <ul class="nav-list">
                <li><a href="/student-course-hub/admin/programmes.php">Programmes</a></li>
                <li><a href="/student-course-hub/admin/modules.php">Modules</a></li>
                <li><a href="/student-course-hub/admin/staff.php">Staff</a></li>
                <li><a href="/student-course-hub/admin/students.php">Students</a></li>
                <li><a href="/student-course-hub/auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>
<main id="main-content" class="admin-main">
    <div class="admin-container">
        <h1>Dashboard</h1>
        <p>Welcome back. Here is your site overview.</p>

        <!-- Stats grid — each number is pulled live from the database -->
        <div class="stats-grid">
            <div class="stat-card">
                <h2><?= e($totalProgrammes) ?></h2>
                <p>Total Programmes</p>
            </div>
            <div class="stat-card">
                <h2><?= e($publishedProgrammes) ?></h2>
                <p>Published Programmes</p>
            </div>
            <div class="stat-card">
                <h2><?= e($totalStudents) ?></h2>
                <p>Interested Students</p>
            </div>
            <div class="stat-card">
                <h2><?= e($totalModules) ?></h2>
                <p>Total Modules</p>
            </div>
        </div>

        <!-- Quick action buttons -->
        <div class="quick-links">
            <h2>Quick Actions</h2>
            <a href="/student-course-hub/admin/programmes.php" class="btn">
                Manage Programmes
            </a>
            <a href="/student-course-hub/admin/students.php" class="btn">
                View Interested Students
            </a>
            <a href="/student-course-hub/admin/modules.php" class="btn">
                Manage Modules
            </a>
        </div>
    </div>
</main>
<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> Student Course Hub &mdash; CTEC2712N</p>
</footer>
</body>
</html>
