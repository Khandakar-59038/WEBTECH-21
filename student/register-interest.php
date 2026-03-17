<?php
// student/register-interest.php --- Process the interest registration form
// CTEC2712N --- Ushno
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// Only accept POST requests --- reject anything else
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
// CSRF check --- prevents fake submissions from other websites
// hash_equals prevents timing attacks during string comparison
if (!isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die('Security check failed. Please go back and try again.');
}
// Get and validate all inputs
$programmeId = filter_input(INPUT_POST, 'programme_id', FILTER_VALIDATE_INT);
$name        = trim($_POST['student_name'] ?? '');
$email       = trim($_POST['email'] ?? '');

$errors = [];

if (!$programmeId) {
    $errors[] = 'Invalid programme selected.';
}
if (empty($name) || mb_strlen($name) > 100) {
    $errors[] = 'Please enter your full name (maximum 100 characters).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
    $errors[] = 'Please enter a valid email address.';
}

// If any validation failed, send back to the form with error message
if (!empty($errors)) {
    $msg = urlencode(implode(' ', $errors));
    redirect('programme.php?id=' . $programmeId . '&error=' . $msg);
}
// Save to database
// ON DUPLICATE KEY handles the case where the same email registers twice
// for the same programme --- it reactivates rather than creating a duplicate
try {
    $stmt = $pdo->prepare(
        'INSERT INTO InterestedStudents
         (ProgrammeID, StudentName, Email, IsActive)
         VALUES (:pid, :name, :email, 1)
         ON DUPLICATE KEY UPDATE
         IsActive = 1,
         StudentName = :name2,
         RegisteredAt = CURRENT_TIMESTAMP'
    );
    $stmt->execute([
        ':pid'   => $programmeId,
        ':name'  => $name,
        ':email' => $email,
        ':name2' => $name,
    ]);

    redirect('programme.php?id=' . $programmeId . '&success=1');

} catch (PDOException $e) {
    error_log('Interest registration error: ' . $e->getMessage());
    $msg = urlencode('Registration failed. Please try again.');
    redirect('programme.php?id=' . $programmeId . '&error=' . $msg);
}