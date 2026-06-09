<?php
require_once __DIR__ . '/config.php';
require_login();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <style>body{font-family:Arial;padding:20px}a{display:inline-block;margin:6px 0}</style>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p><a href="logout.php">Logout</a></p>
    <h3>Editable files</h3>
    <ul>
        <?php foreach ($ALLOWED_FILES as $f): ?>
            <li><a href="editor.php?file=<?php echo urlencode($f); ?>"><?php echo htmlspecialchars($f); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <h3>Quick actions</h3>
    <ul>
        <li><a href="editor.php?file=index.html">Edit index.html</a></li>
    </ul>
</body>
</html>
