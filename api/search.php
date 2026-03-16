<?php
// api/search.php --- Returns JSON results for AJAX live search
// CTEC2712N --- Redoy
require_once '../includes/db.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$search = '%' . addcslashes($q, '%_') . '%';

$stmt = $pdo->prepare(
    'SELECT ProgrammeID, ProgrammeName, Description
     FROM Programmes
     WHERE IsPublished = 1
     AND (ProgrammeName LIKE :q OR Description LIKE :q2)
     LIMIT 10'
);
$stmt->execute([':q' => $search, ':q2' => $search]);
$results = $stmt->fetchAll();

echo json_encode($results);