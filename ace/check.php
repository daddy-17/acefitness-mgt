<?php
require 'config.php';
require 'functions.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) { header('Location: dashboard.php'); exit; }

$status = (strtotime($m['end_date']) >= strtotime(date('Y-m-d'))) ? 'Active' : 'Expired';
?>
<!doctype html><html><head><meta charset="utf-8"><title>Member</title><script src="tailwind.min.css"></script></head>
<body class="bg-gray-50 min-h-screen p-6">
  <a href="members.php" class="text-indigo-600">&larr; Back to members</a>
  <div class="max-w-lg bg-white p-6 rounded shadow mt-4">
    <h2 class="text-lg font-semibold mb-3">Member Details</h2>
    <div><strong><?php echo e($m['first_name'].' '.$m['last_name']); ?></strong></div>
    <div>Phone: <?php echo e($m['phone']); ?></div>
    <div>Email: <?php echo e($m['email']); ?></div>
    <div>Start: <?php echo e($m['start_date']); ?></div>
    <div>End: <?php echo e($m['end_date']); ?></div>
    <div>Status: <strong><?php echo $status; ?></strong></div>
    <div class="mt-3"><a href="renew.php?member_id=<?php echo $m['id']; ?>" class="text-green-600">Renew</a> | <a href="edit_member.php?id=<?php echo $m['id']; ?>" class="text-indigo-600">Edit</a></div>
  </div>
</body></html>
