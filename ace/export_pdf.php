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

$title = 'Members Report';
?>
<!doctype html><html><head>
<meta charset="utf-8"><title><?php echo e($title); ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="tailwindv1.min.css">
<style>
@media print {
  .no-print { display: none; }
}
</style>
</head>
<body class="p-6">
  <div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold"><?php echo e($title); ?></h1>
      <div class="no-print">
        <button onclick="window.print()" class="px-3 py-2 bg-indigo-600 text-white rounded">Print / Save as PDF</button>
      </div>
    </div>

    <table class="w-full table-auto border-collapse">
      <thead><tr class="border-b"><th class="p-2">ID</th><th>Name</th><th>Phone</th><th>End Date</th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr class="border-b">
            <td class="p-2"><?php echo e($r['id']); ?></td>
            <td class="p-2"><?php echo e($r['first_name'].' '.$r['last_name']); ?></td>
            <td class="p-2"><?php echo e($r['phone']); ?></td>
            <td class="p-2"><?php echo e($r['end_date']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body></html>
