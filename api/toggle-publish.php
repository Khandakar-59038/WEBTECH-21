<?php
// api/toggle-publish.php --- Toggle programme published status
// CTEC2712N --- Redoy
session_start();
require_once '../includes/db.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /student-course-hub/admin/programmes.php');
    exit;
}

$stmt = $pdo->prepare('SELECT IsPublished FROM Programmes WHERE ProgrammeID = :id');
$stmt->execute([':id' => $id]);
$prog = $stmt->fetch();

if (!$prog) {
    header('Location: /student-course-hub/admin/programmes.php');
    exit;
}

$newStatus = $prog['IsPublished'] ? 0 : 1;

$stmt = $pdo->prepare('UPDATE Programmes SET IsPublished = :status WHERE ProgrammeID = :id');
$stmt->execute([':status' => $newStatus, ':id' => $id]);

header('Location: /student-course-hub/admin/programmes.php?msg=updated');
exit;