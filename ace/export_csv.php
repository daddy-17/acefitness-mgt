<?php
require 'config.php';
require 'functions.php';
require_login();

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$params = [];
$sql = 'SELECT * FROM members WHERE 1=1';
if ($from) { $sql .= ' AND created_at >= ?'; $params[] = $from.' 00:00:00'; }
if ($to) { $sql .= ' AND created_at <= ?'; $params[] = $to.' 23:59:59'; }
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=members_export_'.date('Ymd_His').'.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['ID','First Name','Last Name','Phone','Email','Start Date','End Date','Created At']);
foreach($rows as $r) {
    fputcsv($out, [$r['id'],$r['first_name'],$r['last_name'],$r['phone'],$r['email'],$r['start_date'],$r['end_date'],$r['created_at']]);
}
fclose($out);
exit;
