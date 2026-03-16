<?php
// student/index.php --- Programme catalogue page
// CTEC2712N --- Prodip
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Fetch all published programmes with level name
$stmt = $pdo->prepare(
    'SELECT p.ProgrammeID, p.ProgrammeName, p.Description,
     p.Image, p.ImageAlt, l.LevelName
     FROM Programmes p
     JOIN Levels l ON p.LevelID = l.LevelID
     WHERE p.IsPublished = 1
     ORDER BY l.LevelName, p.ProgrammeName'
);
$stmt->execute();
$programmes = $stmt->fetchAll();
$pageTitle = 'Programmes --- Student Course Hub';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/student.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<?php require_once '../includes/header.php'; ?>

<!-- Live search bar -->
<div class="search-bar">
    <label for="search-input">Search programmes</label>
    <input type="text"
           id="search-input"
           placeholder="e.g. Computer Science, AI, Cyber Security"
           aria-label="Search programmes by name or description">
</div>

<!-- Level filter buttons -->
<div class="filter-buttons" role="group" aria-label="Filter by level">
    <button class="filter-btn active" data-filter="all">
        All Programmes
    </button>
    <button class="filter-btn" data-filter="Undergraduate">
        Undergraduate
    </button>
    <button class="filter-btn" data-filter="Postgraduate">
        Postgraduate
    </button>
</div>

<!-- Programme card grid -->
<div class="programme-grid" id="programme-grid">

<?php if (empty($programmes)): ?>
    <p>No programmes are currently available. Please check back later.</p>
<?php else: ?>

    <?php foreach ($programmes as $prog): ?>
    <!-- data-level is read by filter.js to show/hide this card -->
    <article class="programme-card"
             data-level="<?= e($prog['LevelName']) ?>">

        <?php if ($prog['Image']): ?>
        <img src="/student-course-hub/images/<?= e($prog['Image']) ?>"
             alt="<?= e($prog['ImageAlt'] ?: $prog['ProgrammeName']) ?>"
             loading="lazy">
        <?php else: ?>
        <!-- Decorative placeholder shown when no image uploaded -->
        <div class="card-no-image" aria-hidden="true"></div>
        <?php endif; ?>

        <h2><?= e($prog['ProgrammeName']) ?></h2>

        <span class="badge"><?= e($prog['LevelName']) ?></span>

        <!-- Truncate description to 150 characters -->
        <p><?= e(mb_substr($prog['Description'], 0, 150)) ?>...</p>

        <a href="programme.php?id=<?= (int)$prog['ProgrammeID'] ?>"
           class="btn"
           aria-label="View details for <?= e($prog['ProgrammeName']) ?>">
            View Details
        </a>

    </article>
    <?php endforeach; ?>

<?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
<script src="/student-course-hub/js/filter.js"></script>
</body>
</html>