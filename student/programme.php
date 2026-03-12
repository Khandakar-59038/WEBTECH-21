<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Step 1: Safely get the programme ID from the URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('index.php');  // Redirect if no valid ID
}

// Step 2: Fetch the programme
$stmt = $pdo->prepare(
    'SELECT p.*, l.LevelName, s.Name AS LeaderName
     FROM Programmes p
     JOIN Levels l ON p.LevelID = l.LevelID
     JOIN Staff  s ON p.ProgrammeLeaderID = s.StaffID
     WHERE p.ProgrammeID = :id AND p.IsPublished = 1'
);
$stmt->execute([':id' => $id]);
$programme = $stmt->fetch();

if (!$programme) {
    redirect('index.php');  // Programme not found
}
// Step 3: Fetch modules grouped by year
$stmt2 = $pdo->prepare(
    'SELECT pm.Year, m.ModuleName, m.Description, s.Name AS Leader
     FROM ProgrammeModules pm
     JOIN Modules m ON pm.ModuleID = m.ModuleID
     JOIN Staff   s ON m.ModuleLeaderID = s.StaffID
     WHERE pm.ProgrammeID = :id
     ORDER BY pm.Year ASC, m.ModuleName ASC'
);
$stmt2->execute([':id' => $id]);
$rows = $stmt2->fetchAll();

// Step 4: Group by year
$modulesByYear = [];
foreach ($rows as $row) {
    $modulesByYear[$row['Year']][] = $row;
}
?>
<!-- HTML output below -->
<h1><?= e($programme['ProgrammeName']) ?></h1>
<p>Level: <?= e($programme['LevelName']) ?></p>
<p>Programme Leader: <?= e($programme['LeaderName']) ?></p>
<p><?= e($programme['Description']) ?></p>

<?php foreach ($modulesByYear as $year => $modules): ?>
<section aria-label='Year <?= $year ?> Modules'>
    <h2>Year <?= $year ?></h2>
    <?php foreach ($modules as $m): ?>
        <div class='module-card'>
            <h3><?= e($m['ModuleName']) ?></h3>
            <p>Leader: <?= e($m['Leader']) ?></p>
            <p><?= e($m['Description']) ?></p>
        </div>
    <?php endforeach; ?>
</section>
<?php endforeach; 
?>
 
