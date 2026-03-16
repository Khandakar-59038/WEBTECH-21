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

