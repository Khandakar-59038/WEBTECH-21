<?php
// Step 1: Connect to the database
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Step 2: Fetch all published programmes
$stmt = $pdo->prepare(
    'SELECT p.ProgrammeID, p.ProgrammeName, p.Description, p.Image,
            p.ImageAlt, l.LevelName
     FROM Programmes p
     JOIN Levels l ON p.LevelID = l.LevelID
     WHERE p.IsPublished = 1
     ORDER BY l.LevelName, p.ProgrammeName'
);
$stmt->execute();
$programmes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Programmes — Student Course Hub</title>
    <link rel='stylesheet' href='../css/main.css'>
    <link rel='stylesheet' href='../css/student.css'>
</head>
<body>
  <main id='main-content'>
  <div class='programme-grid'>
  <?php foreach ($programmes as $prog): ?>
    <article class='programme-card' data-level='<?= e($prog['LevelName']) ?>'>
      <img src='../images/<?= e($prog['Image']) ?>' alt='<?= e($prog['ImageAlt']) ?>'>
      <h2><?= e($prog['ProgrammeName']) ?></h2>
      <span class='badge'><?= e($prog['LevelName']) ?></span>
      <p><?= e($prog['Description']) ?></p>
      <a href='programme.php?id=<?= (int)$prog['ProgrammeID'] ?>' class='btn'>View Details</a>
    </article>
  <?php endforeach; ?>
  </div>
  </main>
</body>
</html>

