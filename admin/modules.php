<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$message = '';

// ── Handle POST actions ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ASSIGN MODULE LEADER
    if ($_POST['action'] === 'assign') {
        $moduleId = (int)$_POST['module_id'];
        $staffId  = (int)$_POST['staff_id'];
        $stmt = $pdo->prepare('UPDATE Modules SET ModuleLeaderID = :staff WHERE ModuleID = :module');
        $stmt->execute([':staff' => $staffId, ':module' => $moduleId]);
        header('Location: /student-course-hub/admin/modules.php?msg=assigned');
        exit;
    }

    // REMOVE MODULE LEADER
    if ($_POST['action'] === 'remove') {
        $moduleId = (int)$_POST['module_id'];
        $stmt = $pdo->prepare('UPDATE Modules SET ModuleLeaderID = NULL WHERE ModuleID = :module');
        $stmt->execute([':module' => $moduleId]);
        header('Location: /student-course-hub/admin/modules.php?msg=removed');
        exit;
    }
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'] === 'assigned' ? 'Module leader assigned.' : 'Module leader removed.';
}

// ── Fetch all modules with current leader ─────────────────────────────────
$modules = $pdo->query(
    'SELECT m.ModuleID, m.ModuleName, m.Description,
            s.StaffID, s.Name AS LeaderName
     FROM Modules m
     LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
     ORDER BY m.ModuleName'
)->fetchAll();

// ── Fetch all staff for dropdown ──────────────────────────────────────────
$staff = $pdo->query('SELECT StaffID, Name FROM Staff ORDER BY Name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules — Admin</title>
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
    <h1>Manage Modules</h1>
    <p>Assign or reassign module leaders for all <?= count($modules) ?> modules.</p>

    <?php if ($message): ?>
        <div class="success-message" role="alert"><?= e($message) ?></div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Module Name</th>
                <th>Current Leader</th>
                <th>Assign New Leader</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($modules as $mod): ?>
            <tr>
                <td><strong><?= e($mod['ModuleName']) ?></strong></td>
                <td>
                    <?php if ($mod['LeaderName']): ?>
                        <span style="color:var(--green,#1E6B3C);font-weight:600">
                            <?= e($mod['LeaderName']) ?>
                        </span>
                    <?php else: ?>
                        <span style="color:#999">No leader assigned</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" style="display:flex;gap:0.5rem;align-items:center">
                        <input type="hidden" name="action" value="assign">
                        <input type="hidden" name="module_id" value="<?= (int)$mod['ModuleID'] ?>">
                        <select name="staff_id" required style="padding:0.3rem 0.5rem;border:1px solid #ccc;border-radius:5px;font-family:inherit">
                            <option value="">— Select —</option>
                            <?php foreach ($staff as $s): ?>
                            <option value="<?= (int)$s['StaffID'] ?>"
                                <?= $s['StaffID'] == $mod['StaffID'] ? 'selected' : '' ?>>
                                <?= e($s['Name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-small">Assign</button>
                    </form>
                </td>
                <td>
                    <?php if ($mod['LeaderName']): ?>
                    <form method="POST" style="display:inline"
                          onsubmit="return confirm('Remove leader from <?= e(addslashes($mod['ModuleName'])) ?>?')">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="module_id" value="<?= (int)$mod['ModuleID'] ?>">
                        <button type="submit" class="btn btn-small btn-danger">Remove</button>
                    </form>
                    <?php endif; ?>
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
