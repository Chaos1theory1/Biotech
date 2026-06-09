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
    <style>body{font-family:Arial;padding:12px}textarea{width:100%;height:60vh;font-family:monospace}</style>
</head>
<body>
    <p><a href="index.php">← Dashboard</a> | <a href="logout.php">Logout</a></p>
    <h3>Editing: <?php echo htmlspecialchars($file); ?></h3>
    <?php if ($error): ?><div style="color:#b00"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color:#080"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <textarea name="content"><?php echo htmlspecialchars($current); ?></textarea>
        <button type="submit">Save</button>
    </form>
</body>
</html>
