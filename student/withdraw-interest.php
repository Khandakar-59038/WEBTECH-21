<?php
// student/withdraw-interest.php --- Withdraw interest (soft delete)
// CTEC2712N --- Ushno
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$email = trim($_GET['email'] ?? '');
$pid   = filter_input(INPUT_GET, 'programme', FILTER_VALIDATE_INT);

if (!$email || !$pid) {
    redirect('index.php');
}
// Soft delete: set IsActive = 0 and record the withdrawal time
// The record stays in the database for audit purposes
$stmt = $pdo->prepare(
    'UPDATE InterestedStudents
     SET IsActive = 0, WithdrawnAt = NOW()
     WHERE Email = :email AND ProgrammeID = :pid'
);
$stmt->execute([':email' => $email, ':pid' => $pid]);
// Get the programme name to show in the confirmation message
$stmt2 = $pdo->prepare(
    'SELECT ProgrammeName FROM Programmes WHERE ProgrammeID = :pid'
);
$stmt2->execute([':pid' => $pid]);
$prog     = $stmt2->fetch();
$progName = $prog ? e($prog['ProgrammeName']) : 'this programme';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interest Withdrawn — Student Course Hub</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/student.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<?php require_once '../includes/header.php'; ?>

<div style="max-width:640px; margin:3rem auto; padding:1.5rem;">
    <h1>Interest Withdrawn</h1>
    <div class="success-message" role="alert">
        Your interest in <strong><?= $progName ?></strong>
        has been successfully withdrawn.
    </div>
    <p>You will no longer receive updates about this programme.</p>
    <p>You can re-register at any time by visiting the programme page.</p>
    <a href="index.php" class="btn">Browse Other Programmes</a>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>