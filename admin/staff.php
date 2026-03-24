<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Fetch all staff from the database
$stmt = $pdo->prepare('SELECT StaffID, Name FROM Staff ORDER BY Name ASC');
$stmt->execute();
$staffList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff — Student Course Hub Admin</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/admin.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<header class="site-header">
    <div class="header-inner">
        <a href="/student-course-hub/admin/index.php" class="site-logo">SCH Admin</a>
        <nav aria-label="Admin navigation">
            <ul class="nav-list">
                <li><a href="/student-course-hub/admin/index.php">Dashboard</a></li>
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
        <h1>Staff Members</h1>
        <p>All <?= count($staffList) ?> staff members in the system.</p>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffList as $staff): ?>
                <tr>
                    <td><?= e($staff['StaffID']) ?></td>
                    <td><?= e($staff['Name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> Student Course Hub &mdash; CTEC2712N</p>
</footer>
</body>
</html>
