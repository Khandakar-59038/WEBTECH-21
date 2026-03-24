<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

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

// Assign a colour index to each staff member for avatar variety
$colours = ['blue', 'green', 'purple', 'teal', 'orange', 'red'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Staff — Student Course Hub</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/student.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<header class="site-header">
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
        <?php foreach ($staffList as $i => $staff): ?>
        <article class="staff-card">

            <?php
            // Build initials — skip titles like Dr. Mr. Prof.
            $titles = ['Dr', 'Mr', 'Mrs', 'Ms', 'Miss', 'Prof', 'Professor'];
            $parts = explode(' ', $staff['Name']);
            $initials = '';
            foreach ($parts as $part) {
                $clean = rtrim($part, '.');
                if (in_array($clean, $titles)) continue;
                if (!empty($part) && ctype_alpha($part[0])) {
                    $initials .= strtoupper($part[0]);
                }
            }
            $initials = substr($initials, 0, 2);
            $colour = $colours[$i % count($colours)];
            ?>

          <?php
$imgFile = 'staff-' . (int)$staff['StaffID'] . '.svg';
$imgPath = $_SERVER['DOCUMENT_ROOT'] . '/student-course-hub/images/' . $imgFile;
?>
<?php if (file_exists($imgPath)): ?>
    <img src="/student-course-hub/images/<?= e($imgFile) ?>"
         alt="Portrait of <?= e($staff['Name']) ?>"
         class="staff-photo">
<?php else: ?>
    <div class="staff-avatar avatar-<?= $colour ?>" aria-hidden="true">
        <?= e($initials) ?>
    </div>
<?php endif; ?>

            <div class="staff-info">
                <h2 class="staff-name"><?= e($staff['Name']) ?></h2>

                <?php if ($staff['Programmes']): ?>
                <div class="staff-section">
                    <h3>Programme Leader</h3>
                    <ul>
                        <?php foreach (explode('||', $staff['Programmes']) as $prog): ?>
                        <li><?= e(trim($prog)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($staff['Modules']): ?>
                <div class="staff-section">
                    <h3>Module Leader</h3>
                    <ul>
                        <?php foreach (explode('||', $staff['Modules']) as $mod): ?>
                        <li><?= e(trim($mod)) ?></li>
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