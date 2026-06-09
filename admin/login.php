<?php
require_once __DIR__ . '/config.php';

// If already logged in, go to dashboard
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // simple credential check
    $user = isset($_POST['username']) ? $_POST['username'] : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';
    global $ADMIN_USER, $ADMIN_PASS;
    if ($user === $ADMIN_USER && $pass === $ADMIN_PASS) {
        $_SESSION['admin_logged'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-frame">
        <h2>Site Admin Login</h2>
        <?php if ($error): ?><div class="message-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="button" type="submit">Login</button>
        </form>
        <p class="login-note">Default credentials: <strong>admin</strong> / <strong>admin123</strong>. Change in <em>admin/config.php</em>.</p>
    </div>
</body>
</html>
