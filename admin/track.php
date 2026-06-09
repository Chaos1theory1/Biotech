<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Use POST']);
    exit;
}

$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$page = isset($_POST['page']) ? trim($_POST['page']) : '';
$label = isset($_POST['label']) ? trim($_POST['label']) : '';

$stats = load_stats();
if (!isset($stats['recentEvents']) || !is_array($stats['recentEvents'])) {
    $stats['recentEvents'] = [];
}
if (!isset($stats['pageViews']) || !is_array($stats['pageViews'])) {
    $stats['pageViews'] = [];
}
if (!isset($stats['clicksByLabel']) || !is_array($stats['clicksByLabel'])) {
    $stats['clicksByLabel'] = [];
}
if (!isset($stats['sharesByLabel']) || !is_array($stats['sharesByLabel'])) {
    $stats['sharesByLabel'] = [];
}

$page = parse_url($page, PHP_URL_PATH) ?: 'unknown';
if ($page === '' || $page === '/') {
    $page = 'home';
}

if ($type === 'visit') {
    $stats['visits'] = ($stats['visits'] ?? 0) + 1;
    $stats['pageViews'][$page] = ($stats['pageViews'][$page] ?? 0) + 1;
    $event = ['type' => 'visit', 'page' => $page, 'label' => '', 'time' => date('c')];
} elseif ($type === 'click') {
    $stats['clicks'] = ($stats['clicks'] ?? 0) + 1;
    if ($label !== '') {
        $stats['clicksByLabel'][$label] = ($stats['clicksByLabel'][$label] ?? 0) + 1;
    }
    $event = ['type' => 'click', 'page' => $page, 'label' => $label, 'time' => date('c')];
} elseif ($type === 'share') {
    $stats['shares'] = ($stats['shares'] ?? 0) + 1;
    if ($label !== '') {
        $stats['sharesByLabel'][$label] = ($stats['sharesByLabel'][$label] ?? 0) + 1;
    }
    $event = ['type' => 'share', 'page' => $page, 'label' => $label, 'time' => date('c')];
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid event type']);
    exit;
}

array_unshift($stats['recentEvents'], $event);
if (count($stats['recentEvents']) > 30) {
    $stats['recentEvents'] = array_slice($stats['recentEvents'], 0, 30);
}

save_stats($stats);

echo json_encode(['success' => true]);
