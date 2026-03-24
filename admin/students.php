<?php
// admin/students.php --- View interested students and export mailing list as CSV
// CTEC2712N --- Redoy
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Handle CSV export
if (isset($_GET['export'])) {
    $pid = filter_input(INPUT_GET, 'export', FILTER_VALIDATE_INT) ?: 0;

    $stmt = $pdo->prepare(
        'SELECT s.StudentName, s.Email, p.ProgrammeName, s.RegisteredAt
         FROM InterestedStudents s
         JOIN Programmes p ON s.ProgrammeID = p.ProgrammeID
         WHERE s.IsActive = 1
         AND (:pid = 0 OR s.ProgrammeID = :pid2)
         ORDER BY p.ProgrammeName, s.RegisteredAt DESC'
    );
    $stmt->execute([':pid' => $pid, ':pid2' => $pid]);
    $rows = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="mailing-list.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name', 'Email', 'Programme', 'Registered At']);
    foreach ($rows as $row) {
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

// Fetch all active interested students
$stmt = $pdo->prepare(
    'SELECT s.InterestID, s.StudentName, s.Email, s.RegisteredAt, p.ProgrammeName
     FROM InterestedStudents s
     JOIN Programmes p ON s.ProgrammeID = p.ProgrammeID
     WHERE s.IsActive = 1
     ORDER BY p.ProgrammeName, s.RegisteredAt DESC'
);
$stmt->execute();
$students = $stmt->fetchAll();

// Count per programme
$countStmt = $pdo->prepare(
    'SELECT p.ProgrammeID, p.ProgrammeName, COUNT(s.InterestID) AS Total
     FROM Programmes p
     LEFT JOIN InterestedStudents s
     ON p.ProgrammeID = s.ProgrammeID AND s.IsActive = 1
     GROUP BY p.ProgrammeID, p.ProgrammeName
     ORDER BY Total DESC'
);
$countStmt->execute();
$counts = $countStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interested Students — Admin</title>
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
            <li><a href="students.php">Students</a></li>
            <li><a href="/student-course-hub/auth/logout.php">Logout</a></li>
        </ul></nav>
    </div>
</header>

<main id="main-content" class="admin-main">
<div class="admin-container">
    <h1>Interested Students</h1>

    <p><a href="?export=0" class="btn">Export ALL as CSV</a></p>

    <section>
        <h2>Interest by Programme</h2>
        <table class="admin-table">
            <thead>
                <tr><th>Programme</th><th>Students</th><th>Export</th></tr>
            </thead>
            <tbody>
            <?php foreach ($counts as $c): ?>
                <tr>
                    <td><?= e($c['ProgrammeName']) ?></td>
                    <td><?= e($c['Total']) ?></td>
                    <td><a href="?export=<?= (int)$c['ProgrammeID'] ?>">Export CSV</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>All Registered Students</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th>
                    <th>Programme</th><th>Registered</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= e($s['StudentName']) ?></td>
                    <td><?= e($s['Email']) ?></td>
                    <td><?= e($s['ProgrammeName']) ?></td>
                    <td><?= e($s['RegisteredAt']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</div>
</main>
</body>
</html>
  session_start();
  require_once '../includes/auth.php';
  requireAdmin();
dmin/students.php
