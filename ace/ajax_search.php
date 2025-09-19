<?php
require 'config.php';
require 'functions.php';
require_login();

$q = $_GET['q'] ?? '';
$per_page = 10;
$page = 1;
list($page, $per_page, $offset) = paginate($page, $per_page);

$params = [];
$sql = "SELECT * FROM members WHERE 1=1 ";
if ($q !== '') {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ?) ";
    $s = '%'.$q.'%';
    $params = [$s,$s,$s,$s];
}

$stmt = $pdo->prepare($sql . " ORDER BY created_at DESC LIMIT ? OFFSET ?");
$params[] = (int)$per_page;
$params[] = (int)$offset;
$stmt->execute($params);
$members = $stmt->fetchAll();

if (!$members) {
    echo '<div class="text-gray-500">No members found.</div>';
    exit;
}
?>
<table class="w-full table-auto border-collapse">
  <thead>
    <tr class="text-left border-b">
      <th class="p-2">ID</th><th>Name</th><th>Phone</th><th>Ends</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($members as $m): ?>
    <tr class="border-b">
      <td class="p-2"><?php echo $m['id']; ?></td>
      <td class="p-2"><?php echo e($m['first_name'].' '.$m['last_name']); ?></td>
      <td class="p-2"><?php echo e($m['phone']); ?></td>
      <td class="p-2"><?php echo e($m['end_date']); ?></td>
      <td class="p-2">
        <a href="edit_member.php?id=<?php echo $m['id']; ?>" class="mr-2 text-indigo-600">Edit</a>
        <a href="renew.php?member_id=<?php echo $m['id']; ?>" class="mr-2 text-green-600">Renew</a>
        <a href="check.php?id=<?php echo $m['id']; ?>" class="text-gray-600">View</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
