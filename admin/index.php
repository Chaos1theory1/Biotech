<?php
require_once __DIR__ . '/config.php';
require_login();

?>
<?php
require_once __DIR__ . '/config.php';
require_login();

$stats = load_stats();
$topPages = $stats['pageViews'];
arsort($topPages);
$topPages = array_slice($topPages, 0, 5, true);
$recentEvents = $stats['recentEvents'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <div class="header-row">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Manage site text, upload images, and view visitor activity.</p>
        </div>
        <div>
            <a class="button" href="upload.php">Upload Images</a>
            <a class="button secondary" href="logout.php">Logout</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total page visits</h3>
            <strong><?php echo intval($stats['visits']); ?></strong>
        </div>
        <div class="stat-card">
            <h3>Total clicks</h3>
            <strong><?php echo intval($stats['clicks']); ?></strong>
        </div>
        <div class="stat-card">
            <h3>Total shares</h3>
            <strong><?php echo intval($stats['shares']); ?></strong>
        </div>
    </div>

    <div class="site-card">
        <h2>Quick actions</h2>
        <div class="button-row">
            <a class="button" href="editor.php?file=index.html">Edit Homepage Text</a>
            <a class="button" href="editor.php?file=services.html">Edit Services</a>
            <a class="button" href="editor.php?file=contact.html">Edit Contact</a>
            <a class="button" href="editor.php?file=a-propos.html">Edit About Page</a>
        </div>
    </div>

    <div class="site-card">
        <h2>Top pages</h2>
        <?php if (count($topPages) === 0): ?>
            <p>No page views recorded yet.</p>
        <?php else: ?>
            <ul class="card-list">
                <?php foreach ($topPages as $page => $count): ?>
                    <li><strong><?php echo htmlspecialchars($page); ?></strong> — <?php echo intval($count); ?> views</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="site-card">
        <h2>Recent events</h2>
        <?php if (empty($recentEvents)): ?>
            <p>No events recorded yet.</p>
        <?php else: ?>
            <ul class="card-list">
                <?php foreach ($recentEvents as $event): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($event['type']); ?></strong> on <em><?php echo htmlspecialchars($event['page']); ?></em>
                        <?php if (!empty($event['label'])): ?> — <?php echo htmlspecialchars($event['label']); ?><?php endif; ?>
                        <div style="font-size:0.85rem;color:#637381;"><?php echo htmlspecialchars($event['time']); ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
