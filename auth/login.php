<?php
// auth/login.php — Admin login page
// CTEC2712N — Musanna Khandakar

session_start();

// If already logged in, go straight to admin dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: /student-course-hub/admin/index.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/helpers.php';

$error = '';

// Process form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Fetch admin by username
        $stmt = $pdo->prepare('SELECT * FROM Admins WHERE Username = :username');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        // Verify password against bcrypt hash
        if ($admin && password_verify($password, $admin['PasswordHash'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['AdminID'];
            $_SESSION['admin_role'] = $admin['Role'];
            header('Location: /student-course-hub/admin/index.php');
            exit;
        } else {
            // Generic error — never say which field is wrong
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
        <p>Student Course Hub — CTEC2712N</p>

        <?php if ($error): ?>
            <div class="error-message" role="alert">
                <?= e($error) ?>
            </div>
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