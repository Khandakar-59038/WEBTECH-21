<?php
// admin/students.php --- View interested students, export CSV, delete registrations
// CTEC2712N --- Redoy
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// ── Handle delete registration ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['interest_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM InterestedStudents WHERE InterestID = :id');
        $stmt->execute([':id' => $id]);
    }
    header('Location: /student-course-hub/admin/students.php?msg=deleted');
    exit;
}

// ── CSV Export ────────────────────────────────────────────────────────────────
$exportId = isset($_GET['export']) ? (int)$_GET['export'] : null;
if ($exportId !== null) {
    $params = [];
    $where = 'WHERE i.IsActive = 1';
    if ($exportId > 0) {
        $where .= ' AND i.ProgrammeID = :pid';
        $params[':pid'] = $exportId;
    }
    $stmt = $pdo->prepare(
        "SELECT i.StudentName, i.Email, p.ProgrammeName, l.LevelName, i.RegisteredAt
        FROM InterestedStudents i
        JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
        JOIN Levels l ON p.LevelID = l.LevelID
        $where
        ORDER BY p.ProgrammeName, i.StudentName"
    );
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="mailing-list.csv"');
    $fh = fopen('php://output', 'w');
    fputcsv($fh, ['Name', 'Email', 'Programme', 'Level', 'Registered At']);
    foreach ($rows as $row) {
        fputcsv($fh, [
            $row['StudentName'],
            $row['Email'],
            $row['ProgrammeName'],
            $row['LevelName'],
            $row['RegisteredAt'],
        ]);
    }
    fclose($fh);
    exit;
}

// ── Flash message ─────────────────────────────────────────────────────────────
$message = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Registration removed.';
}

// ── Count per programme ───────────────────────────────────────────────────────
$counts = $pdo->query(
    'SELECT p.ProgrammeID, p.ProgrammeName, l.LevelName,
    COUNT(i.InterestID) AS total
    FROM Programmes p
    LEFT JOIN InterestedStudents i ON p.ProgrammeID = i.ProgrammeID AND i.IsActive = 1
    JOIN Levels l ON p.LevelID = l.LevelID
    GROUP BY p.ProgrammeID
    ORDER BY total DESC'
)->fetchAll();

// ── All active registrations ──────────────────────────────────────────────────
$students = $pdo->query(
    'SELECT i.InterestID, i.StudentName, i.Email, i.RegisteredAt, i.IsActive,
    p.ProgrammeName, l.LevelName
    FROM InterestedStudents i
    JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
    JOIN Levels l ON p.LevelID = l.LevelID
    ORDER BY i.RegisteredAt DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students --- Admin</title>
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
<h1>Interested Students</h1>

<?php if ($message): ?>
<div class="error-message" role="alert"><?= e($message) ?></div>
<?php endif; ?>

<!-- Export buttons -->
<div style="margin-bottom:1.5rem;display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center">
<a href="?export=0" class="btn">Export All CSV</a>
<span style="color:#666;font-size:0.9rem">or export by programme:</span>
<?php foreach ($counts as $c): ?>
<?php if ($c['total'] > 0): ?>
<a href="?export=<?= (int)$c['ProgrammeID'] ?>" class="btn" style="font-size:0.85rem;padding:0.4rem 0.9rem">
<?= e($c['ProgrammeName']) ?> (<?= $c['total'] ?>)
</a>
<?php endif; ?>
<?php endforeach; ?>
</div>

<!-- Count summary table -->
<h2>Interest by Programme</h2>
<table class="admin-table">
<thead>
<tr><th>Programme</th><th>Level</th><th>Interested Students</th></tr>
</thead>
<tbody>
<?php foreach ($counts as $c): ?>
<tr>
<td><?= e($c['ProgrammeName']) ?></td>
<td><?= e($c['LevelName']) ?></td>
<td><strong><?= (int)$c['total'] ?></strong></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- All registrations table -->
<h2>All Registrations (<?= count($students) ?>)</h2>
<table class="admin-table">
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Programme</th>
<th>Status</th>
<th>Registered</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach ($students as $s): ?>
<tr>
<td><?= e($s['StudentName']) ?></td>
<td><?= e($s['Email']) ?></td>
<td><?= e($s['ProgrammeName']) ?></td>
<td>
<span class="<?= $s['IsActive'] ? 'badge-published' : 'badge-draft' ?>">
<?= $s['IsActive'] ? 'Active' : 'Withdrawn' ?>
</span>
</td>
<td><?= e(date('d M Y', strtotime($s['RegisteredAt']))) ?></td>
<td>
<form method="POST" style="display:inline"
onsubmit="return confirm('Remove this registration permanently?')">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="interest_id" value="<?= (int)$s['InterestID'] ?>">
<button type="submit" class="btn btn-small btn-danger">Remove</button>
</form>
</td>
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
