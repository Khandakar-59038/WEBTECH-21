<<<<<<< HEAD
<?php
// admin/modules.php --- View all modules with their leaders
// CTEC2712N --- Redoy
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$stmt = $pdo->prepare(
    'SELECT m.ModuleID, m.ModuleName, m.Description, s.Name AS Leader
     FROM Modules m
     JOIN Staff s ON m.ModuleLeaderID = s.StaffID
     ORDER BY m.ModuleName'
);
$stmt->execute();
$modules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules --- Admin</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/admin.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>

<header class="site-header">
    <div class="header-inner">
        <a href="/student-course-hub/admin/index.php" class="site-logo">SCH Admin</a>
        <nav aria-label="Admin navigation"><ul class="nav-list">
            <li><a href="programmes.php">Programmes</a></li>
            <li><a href="modules.php">Modules</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="/student-course-hub/auth/logout.php">Logout</a></li>
        </ul></nav>
    </div>
</header>

<main id="main-content" class="admin-main">
<div class="admin-container">

    <h1>Manage Modules</h1>
    <p>Total modules: <?= count($modules) ?></p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Module Name</th>
                <th>Leader</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($modules as $m): ?>
            <tr>
                <td><?= e($m['ModuleName']) ?></td>
                <td><?= e($m['Leader']) ?></td>
                <td><?= e($m['Description']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
</main>
</body>
</html>
=======
  session_start();
  require_once '../includes/auth.php';
  requireAdmin();
in/modules.php
>>>>>>> origin/main
