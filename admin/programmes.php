<?php
// admin/programmes.php --- Manage programmes (add, edit, delete, publish)
// CTEC2712N --- Redoy
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Handle delete
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare('DELETE FROM Programmes WHERE ProgrammeID = :id');
    $stmt->execute([':id' => (int)$_POST['delete_id']]);
    redirect('programmes.php?msg=deleted');
}

// Handle add
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare(
        'INSERT INTO Programmes (ProgrammeName,LevelID,ProgrammeLeaderID,Description,IsPublished)
         VALUES (:name,:level,:leader,:desc,0)'
    );
    $stmt->execute([
        ':name'   => trim($_POST['name']),
        ':level'  => (int)$_POST['level_id'],
        ':leader' => (int)$_POST['leader_id'],
        ':desc'   => trim($_POST['description']),
    ]);
    redirect('programmes.php?msg=added');
}

// Handle edit
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit'])) {
    $stmt = $pdo->prepare(
        'UPDATE Programmes
         SET ProgrammeName=:name, LevelID=:level,
             ProgrammeLeaderID=:leader, Description=:desc
         WHERE ProgrammeID=:id'
    );
    $stmt->execute([
        ':name'   => trim($_POST['name']),
        ':level'  => (int)$_POST['level_id'],
        ':leader' => (int)$_POST['leader_id'],
        ':desc'   => trim($_POST['description']),
        ':id'     => (int)$_POST['programme_id'],
    ]);
    redirect('programmes.php?msg=updated');
}

// Fetch all programmes with level and leader name
$stmt = $pdo->prepare(
    'SELECT p.ProgrammeID, p.ProgrammeName, p.IsPublished,
            l.LevelName, s.Name AS Leader
     FROM Programmes p
     JOIN Levels l ON p.LevelID = l.LevelID
     JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
     ORDER BY p.ProgrammeName'
);
$stmt->execute();
$programmes = $stmt->fetchAll();

// Fetch levels and staff for the add/edit forms
$levels = $pdo->query('SELECT * FROM Levels')->fetchAll();
$staff  = $pdo->query('SELECT * FROM Staff ORDER BY Name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programmes — Admin</title>
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
    <h1>Manage Programmes</h1>

    <?php if (isset($_GET['msg'])): ?>
    <div class="success-message" role="alert">
        <?php
        if ($_GET['msg']==='added')   echo 'Programme added successfully.';
        if ($_GET['msg']==='deleted') echo 'Programme deleted.';
        if ($_GET['msg']==='updated') echo 'Programme updated.';
        ?>
    </div>
    <?php endif; ?>

    <!-- Add New Programme Form -->
    <section>
        <h2>Add New Programme</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="prog-name">Programme Name</label>
                <input type="text" id="prog-name" name="name" required maxlength="255">
            </div>
            <div class="form-group">
                <label for="level_id">Level</label>
                <select id="level_id" name="level_id" required>
                    <?php foreach ($levels as $l): ?>
                    <option value="<?= e($l['LevelID']) ?>"><?= e($l['LevelName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="leader_id">Programme Leader</label>
                <select id="leader_id" name="leader_id" required>
                    <?php foreach ($staff as $s): ?>
                    <option value="<?= e($s['StaffID']) ?>"><?= e($s['Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            <button type="submit" name="add" class="btn">Add Programme</button>
        </form>
    </section>

    <!-- Programmes Table -->
    <section>
        <h2>All Programmes</h2>
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
                    <td><?= e($prog['Leader']) ?></td>
                    <td>
                        <span class="<?= $prog['IsPublished'] ? 'badge-published' : 'badge-draft' ?>">
                            <?= $prog['IsPublished'] ? 'Published' : 'Draft' ?>
                        </span>
                    </td>
                    <td>
                        <!-- Toggle publish button -->
                        <form method="POST"
                              action="/student-course-hub/api/toggle-publish.php"
                              style="display:inline">
                            <input type="hidden" name="id"
                                   value="<?= e($prog['ProgrammeID']) ?>">
                            <button type="submit" class="btn">
                                <?= $prog['IsPublished'] ? 'Unpublish' : 'Publish' ?>
                            </button>
                        </form>
                        <!-- Delete button -->
                        <form method="POST" action="" style="display:inline">
                            <input type="hidden" name="delete_id"
                                   value="<?= e($prog['ProgrammeID']) ?>">
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Delete this programme? This cannot be undone.')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</div>
</main>
</body>
</html>
