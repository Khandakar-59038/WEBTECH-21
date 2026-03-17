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
<!-- Modules grouped by year -->
<section class="modules-section">
    <h2>Programme Modules</h2>

    <?php if (empty($modulesByYear)): ?>
        <p>No modules listed for this programme yet.</p>
    <?php else: ?>

        <?php foreach ($modulesByYear as $year => $modules): ?>
        <section aria-label="Year <?= $year ?> modules">
            <h3>Year <?= $year ?></h3>
            <div class="modules-grid">
                <?php foreach ($modules as $m): ?>
                <div class="module-card">
                    <h4><?= e($m['ModuleName']) ?></h4>
                    <p class="module-leader">
                        Leader: <?= e($m['Leader']) ?>
                    </p>
                    <p><?= e($m['Description']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>

    <?php endif; ?>
</section>
<!-- Interest registration form -->
<section class="interest-section">
    <h2>Register Your Interest</h2>
    <p>Enter your details and we will keep you informed about this programme.</p>

    <?php if (isset($_GET['success'])): ?>
    <div class="success-message" role="alert">
        Your interest has been registered. We will be in touch!
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="error-message" role="alert">
        <?= e(urldecode($_GET['error'])) ?>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="register-interest.php"
          id="interest-form"
          novalidate>

        <!-- Hidden: programme ID passed to processing page -->
        <input type="hidden"
               name="programme_id"
               value="<?= (int)$programme['ProgrammeID'] ?>">

        <!-- Hidden: CSRF token prevents fake form submissions -->
        <input type="hidden"
               name="csrf_token"
               value="<?= e($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label for="student-name">Full Name</label>
            <input type="text"
                   id="student-name"
                   name="student_name"
                   required
                   maxlength="100"
                   autocomplete="name"
                   aria-describedby="name-error">
            <span id="name-error"
                  class="field-error"
                  role="alert"
                  aria-live="polite"></span>
        </div>

        <div class="form-group">
            <label for="student-email">Email Address</label>
            <input type="email"
                   id="student-email"
                   name="email"
                   required
                   maxlength="255"
                   autocomplete="email"
                   aria-describedby="email-error">
            <span id="email-error"
                  class="field-error"
                  role="alert"
                  aria-live="polite"></span>
        </div>

        <button type="submit" class="btn">Register Interest</button>
    </form>
</section>

<p><a href="index.php">Back to all programmes</a></p>

<?php require_once '../includes/footer.php'; ?>
<script src="/student-course-hub/js/validation.js"></script>
</body>
</html>