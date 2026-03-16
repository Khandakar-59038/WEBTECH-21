<?php
// admin/modules.php --- View all modules with their leaders
// CTEC2712N --- Redoy
session_start();
require_once '../includes/db.php';
require_once '../includes/helpers.php';

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Fetch all modules with leader name
$stmt = $pdo->prepare(
    'SELECT m.ModuleID, m.ModuleName, m.Description, s.Name AS Leader
     FROM Modules m
     JOIN Staff s ON m.ModuleLeaderID = s.StaffID
     ORDER BY m.ModuleName'
);
$stmt->execute();
$modules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules — Admin</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/admin.css">
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>

<header class="site-header">
    <div class="header-inner"