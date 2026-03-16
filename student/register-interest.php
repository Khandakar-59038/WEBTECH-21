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