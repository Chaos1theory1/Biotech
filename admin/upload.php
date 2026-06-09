<?php
require_once __DIR__ . '/config.php';
require_login();

$error = '';
$success = '';
$imageDir = image_dir_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select an image to upload.';
    } else {
        $file = $_FILES['image'];
        $filename = sanitize_image_filename($file['name']);
        $target = $imageDir . $filename;

        if (!allowed_image_extension($filename)) {
            $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp, svg.';
        } elseif ($file['size'] > $MAX_UPLOAD_SIZE) {
            $error = 'File is too large. Max size is 5 MB.';
        } elseif (!is_dir($imageDir) || !is_writable($imageDir)) {
            $error = 'Upload directory is not writable. Check permissions.';
        } else {
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $success = 'Image uploaded successfully: ' . htmlspecialchars($filename);
            } else {
                $error = 'Unable to move uploaded file. Check permissions.';
            }
        }
    }
}

$images = [];
if (is_dir($imageDir)) {
    $glob = glob($imageDir . '*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE);
    if ($glob !== false) {
        foreach ($glob as $path) {
            if (is_file($path)) {
                $images[] = $path;
            }
        }
    }
}

?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Upload Images</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <div class="header-row">
        <h2>Image Upload</h2>
        <div>
            <a class="button secondary" href="index.php">Back to dashboard</a>
            <a class="button secondary" href="logout.php">Logout</a>
        </div>
    </div>

    <?php if ($error): ?><div class="message-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="message-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="site-card">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select a new image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <button class="button" type="submit">Upload Image</button>
        </form>
    </div>

    <div class="site-card">
        <h3>Current images</h3>
        <?php if (count($images) === 0): ?>
            <p>No images found in the <code>images/</code> folder yet.</p>
        <?php else: ?>
            <div class="image-grid">
                <?php foreach ($images as $path): $name = basename($path); ?>
                    <div class="image-tile">
                        <img src="<?php echo htmlspecialchars(relative_image_url($path)); ?>" alt="<?php echo htmlspecialchars($name); ?>">
                        <p><?php echo htmlspecialchars($name); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
