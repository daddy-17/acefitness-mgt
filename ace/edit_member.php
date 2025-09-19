<?php
require 'config.php';
require 'functions.php';
require_login();
csrf_check();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Fetch the member
$member = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
    $stmt->execute([$id]);
    $member = $stmt->fetch();
    if (!$member) { $member = null; }
}

// Fetch all plans
$plans = $pdo->query('SELECT * FROM plans ORDER BY duration_months ASC')->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $plan_name = $_POST['plan'] ?? '';
    $end_date = $_POST['end_date'] ?? date('Y-m-d');

    // Validation
    if ($first === '' || $last === '' || $phone === '') $errors[] = 'First name, last name, and phone are required';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
    if (!$plan_name) $errors[] = 'Plan is required';

    if (!$errors) {
        // Update member
        $stmt = $pdo->prepare('UPDATE members SET first_name=?, last_name=?, email=?, phone=?, plan=?, end_date=? WHERE id=?');
        $stmt->execute([$first, $last, $email, $phone, $plan_name, $end_date, $id]);

        log_action($pdo, 'edit_member', "Updated member {$id}");
        header('Location: members.php');
        exit;
    }
}

$csrf = csrf_get_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Member</title>
  <script src="tailwind.min.css"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
  <a href="members.php" class="text-indigo-600">&larr; Back to members</a>
  <div class="max-w-lg bg-white p-6 rounded shadow mt-4">
    <h2 class="text-lg font-semibold mb-3">Edit Member</h2>

    <?php if($errors): ?>
      <div class="bg-red-50 p-3 mb-3">
        <?php foreach($errors as $e) echo '<div class="text-red-700">'.e($e).'</div>'; ?>
      </div>
    <?php endif; ?>

    <?php if($member): ?>
    <form method="post" class="space-y-3">
      <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
      <input type="hidden" name="id" value="<?php echo e($id); ?>">

      <div><label class="block text-sm">First Name</label>
        <input name="first_name" class="w-full p-2 border rounded" value="<?php echo e($member['first_name']); ?>" required>
      </div>

      <div><label class="block text-sm">Last Name</label>
        <input name="last_name" class="w-full p-2 border rounded" value="<?php echo e($member['last_name']); ?>" required>
      </div>

      <div><label class="block text-sm">Phone</label>
        <input name="phone" class="w-full p-2 border rounded" value="<?php echo e($member['phone']); ?>" required>
      </div>

      <div><label class="block text-sm">Email</label>
        <input name="email" class="w-full p-2 border rounded" value="<?php echo e($member['email']); ?>">
      </div>

      <div><label class="block text-sm">Plan</label>
        <select name="plan" class="w-full p-2 border rounded" required>
          <option value="">Select a plan</option>
          <?php foreach($plans as $p): ?>
            <option value="<?php echo e($p['name']); ?>" <?php if($member['plan'] == $p['name']) echo 'selected'; ?>>
              <?php echo e($p['name'].' — '.$p['duration_months'].' month(s)'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div><label class="block text-sm">End Date</label>
        <input type="date" name="end_date" class="w-full p-2 border rounded" value="<?php echo e($member['end_date']); ?>" required>
      </div>

      <div>
        <button class="bg-green-600 text-white px-3 py-2 rounded">Update Member</button>
      </div>
    </form>
    <?php else: ?>
      <div class="text-red-600">Member not found.</div>
    <?php endif; ?>
  </div>
</body>
</html>
