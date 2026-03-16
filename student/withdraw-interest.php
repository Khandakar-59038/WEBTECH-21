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