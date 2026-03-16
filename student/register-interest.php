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
