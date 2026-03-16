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