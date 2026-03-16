<?php
// student/programme.php --- Programme detail page
// CTEC2712N --- Ushno
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Get and validate the programme ID from URL (?id=5)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('index.php');
}

// Fetch the programme with its level and leader name
$stmt = $pdo->prepare(
    'SELECT p.ProgrammeID, p.ProgrammeName, p.Description,
     p.Image, p.ImageAlt, l.LevelName, s.Name AS LeaderName
     FROM Programmes p
     JOIN Levels l ON p.LevelID = l.LevelID
     JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
     WHERE p.ProgrammeID = :id
     AND p.IsPublished = 1'
);
$stmt->execute([':id' => $id]);
$programme = $stmt->fetch();

// If the programme does not exist or is not published, redirect
if (!$programme) {
    redirect('index.php');
}

// Fetch all modules for this programme, sorted by year then name
$stmt2 = $pdo->prepare(
    'SELECT pm.Year, m.ModuleName, m.Description, s.Name AS Leader
     FROM ProgrammeModules pm
     JOIN Modules m ON pm.ModuleID = m.ModuleID
     JOIN Staff s ON m.ModuleLeaderID = s.StaffID
     WHERE pm.ProgrammeID = :id
     ORDER BY pm.Year ASC, m.ModuleName ASC'
);
$stmt2->execute([':id' => $id]);
$rows = $stmt2->fetchAll();

// Group modules by year:
// $modulesByYear[1] = [all year 1 modules]
// $modulesByYear[2] = [all year 2 modules] etc.
$modulesByYear = [];
foreach ($rows as $row) {
    $modulesByYear[$row['Year']][] = $row;
}
// Generate CSRF token to protect the interest registration form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($programme['ProgrammeName']) ?> — Student Course Hub</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/student.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<?php require_once '../includes/header.php'; ?>

<!-- Hero section with programme name and level -->
<div class="programme-hero">
    <h1><?= e($programme['ProgrammeName']) ?></h1>
    <span class="badge"><?= e($programme['LevelName']) ?></span>
    <p class="programme-leader">
        Programme Leader: <?= e($programme['LeaderName']) ?>
    </p>
</div>

<!-- About description -->
<section class="programme-description">
    <h2>About This Programme</h2>
    <p><?= e($programme['Description']) ?></p>
</section>