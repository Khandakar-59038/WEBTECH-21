<?php
// auth/login.php — Admin login page
// CTEC2712N — Musanna Khandakar
session_start();

// If already logged in, go straight to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: /student-course-hub/admin/index.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Look up admin by username
        $stmt = $pdo->prepare('SELECT * FROM Admins WHERE Username = :username');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        // password_verify compares the typed password against the bcrypt hash
        if ($admin && password_verify($password, $admin['PasswordHash'])) {
            // Login successful
            session_regenerate_id(true); // prevents session fixation attack
            $_SESSION['admin_id']   = $admin['AdminID'];
            $_SESSION['admin_name'] = $admin['Username'];
            $_SESSION['admin_role'] = $admin['Role'];
            header('Location: /student-course-hub/admin/index.php');
            exit;
        } else {
            $error = 'Invalid credentials. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Student Course Hub</title>
    <link rel="stylesheet" href="/student-course-hub/css/main.css">
    <link rel="stylesheet" href="/student-course-hub/css/admin.css">
</head>
<body>
<main id="main-content" class="login-page">
    <div class="login-box">
        <h1>Admin Login</h1>
        <p>Student Course Hub &mdash; CTEC2712N</p>

        <?php if ($error): ?>
            <div class="error-message" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text"
                       id="username"
                       name="username"
                       required
                       autocomplete="username"
                       value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       autocomplete="current-password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</main>
</body>
</html>
