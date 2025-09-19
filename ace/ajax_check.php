<?php
require 'config.php';
require 'functions.php';
require_login();

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode(['error' => 'Empty query']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM members WHERE phone = ? OR id = ? LIMIT 1');
$stmt->execute([$q, $q]);
$m = $stmt->fetch();
if (!$m) {
    echo json_encode(['error' => 'Member not found']);
    exit;
}

$status = (strtotime($m['end_date']) >= strtotime(date('Y-m-d'))) ? 'Active' : 'Expired';
echo json_encode([
    'name' => $m['first_name'].' '.$m['last_name'],
    'status' => $status,
    'end_date' => $m['end_date']
]);
