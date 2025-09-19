<?php
require 'config.php';
require 'functions.php';
require_login();

// simple report filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$params = [];
$sql = 'SELECT * FROM members WHERE 1=1';
if ($from) { $sql .= ' AND created_at >= ?'; $params[] = $from.' 00:00:00'; }
if ($to) { $sql .= ' AND created_at <= ?'; $params[] = $to.' 23:59:59'; }
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

$csrf = csrf_get_token();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Reports</title><script src="tailwind.min.css"></script></head>
<body class="bg-gray-50 min-h-screen p-6">
  <a href="dashboard.php" class="text-indigo-600">&larr; Back</a>
  <div class="max-w-4xl bg-white p-6 rounded shadow mt-4">
    <h2 class="text-lg font-semibold mb-3">Reports</h2>
    <form method="get" class="flex gap-2 mb-4">
      <input type="date" name="from" value="<?php echo e($from); ?>" class="p-2 border rounded">
      <input type="date" name="to" value="<?php echo e($to); ?>" class="p-2 border rounded">
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Filter</button>
      <a href="export_csv.php?from=<?php echo e($from); ?>&to=<?php echo e($to); ?>" class="px-3 py-2 bg-slate-600 text-white rounded">Export CSV</a>
      <a href="export_pdf.php?from=<?php echo e($from); ?>&to=<?php echo e($to); ?>" class="px-3 py-2 bg-slate-600 text-white rounded">Printable Report (Save as PDF)</a>
    </form>

    <table class="w-full table-auto border-collapse">
      <thead><tr class="border-b"><th class="p-2">ID</th><th>Name</th><th>Phone</th><th>End Date</th></tr></thead>
      <tbody>
        <?php foreach($members as $m): ?>
          <tr class="border-b">
            <td class="p-2"><?php echo $m['id']; ?></td>
            <td class="p-2"><?php echo e($m['first_name'].' '.$m['last_name']); ?></td>
            <td class="p-2"><?php echo e($m['phone']); ?></td>
            <td class="p-2"><?php echo e($m['end_date']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body></html>
