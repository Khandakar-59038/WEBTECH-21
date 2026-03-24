<?php
// admin/programmes.php --- Full programme management (add, edit, delete, publish)
// CTEC2712N --- Redoy
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$message = '';
$messageType = '';
$editProgramme = null;

// ── Handle POST actions ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// DELETE
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['programme_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM Programmes WHERE ProgrammeID = :id');
        $stmt->execute([':id' => $id]);
    }
    header('Location: /student-course-hub/admin/programmes.php?msg=deleted');
    exit;
}

// TOGGLE PUBLISH
if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id = (int)($_POST['programme_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE Programmes SET IsPublished = CASE WHEN IsPublished = 1 THEN 0 ELSE 1 END WHERE ProgrammeID = :id');
        $stmt->execute([':id' => $id]);
    }
    header('Location: /student-course-hub/admin/programmes.php?msg=toggled');
    exit;
}

// ADD NEW
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['programme_name'] ?? '');
    $level = (int)($_POST['level_id'] ?? 0);
    $leader= (int)($_POST['leader_id'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    if ($name && $level && $leader) {
        $stmt = $pdo->prepare(
            'INSERT INTO Programmes (ProgrammeName, LevelID, ProgrammeLeaderID, Description, IsPublished)
            VALUES (:name, :level, :leader, :desc, 0)'
        );
        $stmt->execute([':name'=>$name,':level'=>$level,':leader'=>$leader,':desc'=>$desc]);
    }
    header('Location: /student-course-hub/admin/programmes.php?msg=added');
    exit;
}

// EDIT / UPDATE
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['programme_id'] ?? 0);
    $name = trim($_POST['programme_name'] ?? '');
    $level = (int)($_POST['level_id'] ?? 0);
    $leader= (int)($_POST['leader_id'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    if ($id > 0 && $name && $level && $leader) {
        $stmt = $pdo->prepare(
            'UPDATE Programmes
            SET ProgrammeName = :name,
            LevelID = :level,
            ProgrammeLeaderID = :leader,
            Description = :desc
            WHERE ProgrammeID = :id'
        );
        $stmt->execute([':name'=>$name,':level'=>$level,':leader'=>$leader,':desc'=>$desc,':id'=>$id]);
    }
    header('Location: /student-course-hub/admin/programmes.php?msg=updated');
    exit;
}
}

// ── Load edit form data if ?edit=ID in URL ────────────────────────────────
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM Programmes WHERE ProgrammeID = :id');
    $stmt->execute([':id' => $editId]);
    $editProgramme = $stmt->fetch();
}

// ── Flash messages ────────────────────────────────────────────────────────────
$msgs = ['added'=>'Programme added.','deleted'=>'Programme deleted.','updated'=>'Programme updated.','toggled'=>'Publish status changed.'];
if (isset($_GET['msg']) && isset($msgs[$_GET['msg']])) {
    $message = $msgs[$_GET['msg']];
    $messageType = $_GET['msg'] === 'deleted' ? 'error-message' : 'success-message';
}

// ── Fetch all programmes ──────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT p.*, l.LevelName, s.Name AS LeaderName
    FROM Programmes p
    JOIN Levels l ON p.LevelID = l.LevelID
    LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
    ORDER BY l.LevelName, p.ProgrammeName'
);
$stmt->execute();
$programmes = $stmt->fetchAll();

// ── Fetch levels and staff for dropdowns ─────────────────────────────────
$levels = $pdo->query('SELECT * FROM Levels ORDER BY LevelName')->fetchAll();
$staff = $pdo->query('SELECT StaffID, Name FROM Staff ORDER BY Name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Programmes --- Admin</title>
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
<h1><?= $editProgramme ? 'Edit Programme' : 'Manage Programmes' ?></h1>

<?php if ($message): ?>
<div class="<?= $messageType ?>" role="alert"><?= e($message) ?></div>
<?php endif; ?>

<?php if ($editProgramme): ?>
<!-- ── EDIT FORM ─────────────────────────────────────────────── -->
<h2>Editing: <?= e($editProgramme['ProgrammeName']) ?></h2>
<form method="POST" action="">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="programme_id" value="<?= (int)$editProgramme['ProgrammeID'] ?>">
<div class="form-group">
<label for="edit_name">Programme Name</label>
<input type="text" id="edit_name" name="programme_name"
value="<?= e($editProgramme['ProgrammeName']) ?>" required>
</div>
<div class="form-group">
<label for="edit_level">Level</label>
<select id="edit_level" name="level_id" required>
<?php foreach ($levels as $lv): ?>
<option value="<?= (int)$lv['LevelID'] ?>"
<?= $lv['LevelID'] == $editProgramme['LevelID'] ? 'selected' : '' ?>>
<?= e($lv['LevelName']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label for="edit_leader">Programme Leader</label>
<select id="edit_leader" name="leader_id" required>
<?php foreach ($staff as $s): ?>
<option value="<?= (int)$s['StaffID'] ?>"
<?= $s['StaffID'] == $editProgramme['ProgrammeLeaderID'] ? 'selected' : '' ?>>
<?= e($s['Name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label for="edit_desc">Description</label>
<textarea id="edit_desc" name="description" rows="4"><?= e($editProgramme['Description']) ?></textarea>
</div>
<button type="submit" class="btn">Save Changes</button>
<a href="/student-course-hub/admin/programmes.php" class="btn btn-secondary" style="margin-left:0.5rem">Cancel</a>
</form>

<?php else: ?>
<!-- ── ADD FORM ──────────────────────────────────────────────── -->
<h2>Add New Programme</h2>
<form method="POST" action="">
<input type="hidden" name="action" value="add">
<div class="form-group">
<label for="programme_name">Programme Name</label>
<input type="text" id="programme_name" name="programme_name" required
placeholder="e.g. BSc Computer Science">
</div>
<div class="form-group">
<label for="level_id">Level</label>
<select id="level_id" name="level_id" required>
<option value="">--- Select Level ---</option>
<?php foreach ($levels as $lv): ?>
<option value="<?= (int)$lv['LevelID'] ?>"><?= e($lv['LevelName']) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label for="leader_id">Programme Leader</label>
<select id="leader_id" name="leader_id" required>
<option value="">--- Select Leader ---</option>
<?php foreach ($staff as $s): ?>
<option value="<?= (int)$s['StaffID'] ?>"><?= e($s['Name']) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label for="description">Description</label>
<textarea id="description" name="description" rows="4"
placeholder="Describe the programme..."></textarea>
</div>
<button type="submit" class="btn">Add Programme</button>
</form>
<?php endif; ?>

<!-- ── PROGRAMMES TABLE ──────────────────────────────────────── -->
<h2>All Programmes (<?= count($programmes) ?>)</h2>
<table class="admin-table">
<thead>
<tr>
<th>Name</th>
<th>Level</th>
<th>Leader</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($programmes as $prog): ?>
<tr>
<td><?= e($prog['ProgrammeName']) ?></td>
<td><?= e($prog['LevelName']) ?></td>
<td><?= e($prog['LeaderName'] ?? '---') ?></td>
<td>
<span class="<?= $prog['IsPublished'] ? 'badge-published' : 'badge-draft' ?>">
<?= $prog['IsPublished'] ? 'Published' : 'Draft' ?>
</span>
</td>
<td class="action-btns">
<!-- EDIT -->
<a href="?edit=<?= (int)$prog['ProgrammeID'] ?>" class="btn btn-small">Edit</a>

<!-- TOGGLE PUBLISH -->
<form method="POST" style="display:inline">
<input type="hidden" name="action" value="toggle">
<input type="hidden" name="programme_id" value="<?= (int)$prog['ProgrammeID'] ?>">
<button type="submit" class="btn btn-small <?= $prog['IsPublished'] ? 'btn-warning' : '' ?>">
<?= $prog['IsPublished'] ? 'Unpublish' : 'Publish' ?>
</button>
</form>

<!-- DELETE -->
<form method="POST" style="display:inline"
onsubmit="return confirm('Delete <?= e(addslashes($prog['ProgrammeName'])) ?>?')">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="programme_id" value="<?= (int)$prog['ProgrammeID'] ?>">
<button type="submit" class="btn btn-small btn-danger">Delete</button>
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
