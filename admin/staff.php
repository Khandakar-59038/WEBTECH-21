<?php //admin/staff.php<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();                          // Redirects to login if not logged in
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/header.php';
// ... rest of your code below
