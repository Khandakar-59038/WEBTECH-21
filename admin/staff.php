<?php
// student/staff.php --- Staff profile page
// CTEC2712N --- shows programme leaders and module leaders
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Fetch all staff with their programmes and modules
$stmt = $pdo->prepare(
    'SELECT s.StaffID, s.Name,
    GROUP_CONCAT(DISTINCT p.ProgrammeName ORDER BY p.ProgrammeName SEPARATOR "||") AS Programmes,
    GROUP_CONCAT(DISTINCT m.ModuleName ORDER BY m.ModuleName SEPARATOR "||") AS Modules
    FROM Staff s
    LEFT JOIN Programmes p ON p.ProgrammeLeaderID = s.StaffID AND p.IsPublished = 1
    LEFT JOIN Modules m ON m.ModuleLeaderID = s.StaffID
    GROUP BY s.StaffID
    ORDER BY s.Name'
);
$stmt->execute();
$staffList = $stmt->fetchAll();

$pageTitle = 'Our Staff --- Student Course Hub';
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
<<<<<<< HEAD
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<link rel="stylesheet" href="/student-course-hub/css/main.css">
<link rel="stylesheet" href="/student-course-hub/css/student.css">
=======
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff — Student Course Hub Admin</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/admin.css">
>>>>>>> origin/main
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<header class="site-header">
<<<<<<< HEAD
<div class="header-inner">
<a href="/student-course-hub/student/index.php" class="site-logo">Student Course Hub</a>
<nav aria-label="Main navigation">
<ul class="nav-list">
<li><a href="/student-course-hub/student/index.php">Programmes</a></li>
<li><a href="/student-course-hub/student/staff.php" aria-current="page">Staff</a></li>
</ul>
</nav>
</div>
</header>

<main id="main-content">
<section class="page-hero">
<div class="page-hero-inner">
<h1>Meet Our Staff</h1>
<p>Programme leaders and module leaders dedicated to your success.</p>
</div>
</section>

<div class="staff-grid">
<?php foreach ($staffList as $staff): ?>
<article class="staff-card">
<!-- Avatar circle with initials -->
<div class="staff-avatar" aria-hidden="true">
<?php
$parts = explode(' ', $staff['Name']);
$initials = '';
foreach ($parts as $part) {
    if (ctype_alpha($part[0] ?? '')) $initials .= strtoupper($part[0]);
}
echo e(substr($initials, 0, 2));
?>
</div>

<div class="staff-info">
<h2 class="staff-name"><?= e($staff['Name']) ?></h2>

<?php if ($staff['Programmes']): ?>
<div class="staff-section">
<h3>Programme Leader</h3>
<ul>
<?php foreach (explode('||', $staff['Programmes']) as $prog): ?>
<li><?= e($prog) ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if ($staff['Modules']): ?>
<div class="staff-section">
<h3>Module Leader</h3>
<ul>
<?php foreach (explode('||', $staff['Modules']) as $mod): ?>
<li><?= e($mod) ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if (!$staff['Programmes'] && !$staff['Modules']): ?>
<p class="staff-no-role">Staff member</p>
<?php endif; ?>
</div>
</article>
<?php endforeach; ?>
</div>
</main>

<footer class="site-footer">
<p>&copy; <?= date('Y') ?> Student Course Hub &mdash; CTEC2712N</p>
</footer>
</body>
</html>
=======
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
>>>>>>> origin/main
