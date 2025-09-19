<?php
require_once 'config.php';

// Basic validation helpers
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function log_action($pdo, $action, $details='') {
    $stmt = $pdo->prepare('INSERT INTO logs (action, details, created_at) VALUES (?, ?, NOW())');
    $stmt->execute([$action, $details]);
}

// Pagination helper
function paginate($page, $per_page) {
    $p = max(1, (int)$page);
    $per_page = max(1, (int)$per_page);
    $offset = ($p - 1) * $per_page;
    return [$p, $per_page, $offset];
}
?>