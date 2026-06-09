<?php
require_once __DIR__ . '/config.php';
require_login();

$error = '';
$success = '';

if (empty($_GET['file'])) {
    die('No file specified');
}

$file = sanitize_filename($_GET['file']);
if (!allowed_file($file)) {
    die('File not allowed');
}

$path = file_path($file);
if (!file_exists($path)) {
    die('File does not exist: ' . htmlspecialchars($file));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    // create a backup
    $bak = $path . '.bak.' . date('Ymd_His');
    copy($path, $bak);
    // write new content
    $written = file_put_contents($path, $content);
    if ($written === false) {
        $error = 'Failed to write file (check permissions)';
    } else {
        $success = 'Saved successfully. Backup: ' . basename($bak);
    }
}

$current = file_get_contents($path);

?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editing <?php echo htmlspecialchars($file); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <div class="header-row">
        <div>
            <h1>Editing <?php echo htmlspecialchars($file); ?></h1>
            <p>Update visible text on your site pages and save live changes.</p>
        </div>
        <div>
            <a class="button secondary" href="index.php">Back to dashboard</a>
            <a class="button secondary" href="logout.php">Logout</a>
        </div>
    </div>

    <?php if ($error): ?><div class="message-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="message-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="site-card">
        <form method="post">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <div class="form-group">
                <label>Page content</label>
                <textarea name="content"><?php echo htmlspecialchars($current); ?></textarea>
            </div>
            <button class="button" type="submit">Save changes</button>
        </form>
    </div>
</div>
</body>
</html>
