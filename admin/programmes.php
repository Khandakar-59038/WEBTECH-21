<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();                          // Redirects to login if not logged in
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/header.php';
// ... rest of your code below

$stmt = $pdo->prepare(
    'SELECT p.ProgrammeID, p.ProgrammeName, p.IsPublished, l.LevelName
     FROM Programmes p
     JOIN Levels l ON p.LevelID = l.LevelID
     ORDER BY p.ProgrammeName ASC'
);
$stmt->execute();
$programmes = $stmt->fetchAll();

foreach ($programmes as $prog) {
    echo '<tr>';
    echo '<td>' . e($prog['ProgrammeName']) . '</td>';
    echo '<td>' . e($prog['LevelName']) . '</td>';
    echo '<td>' . ($prog['IsPublished'] ? 'Published' : 'Draft') . '</td>';
    echo '</tr>';
}
