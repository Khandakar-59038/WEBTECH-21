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