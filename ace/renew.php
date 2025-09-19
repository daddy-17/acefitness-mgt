<?php
require 'config.php';
require 'functions.php';
require_login();
csrf_check();

$member_id = (int)($_GET['member_id'] ?? $_POST['member_id'] ?? 0);

$member = null;
if ($member_id) {
    $stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();
    if (!$member) { $member = null; }
}

$plans = $pdo->query('SELECT * FROM plans ORDER BY duration_months ASC')->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $plan_name = $_POST['plan'] ?? '';

    if (!$member_id || !$plan_name) $errors[] = 'Member and plan required';

    if (!$errors) {
        // get member and plan info
        $stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();

        $stmt = $pdo->prepare('SELECT * FROM plans WHERE name = ?');
        $stmt->execute([$plan_name]);
        $plan = $stmt->fetch();

        if (!$member || !$plan) {
            $errors[] = 'Invalid member or plan';
        } else {
            // compute new end date: take max(current end, today) then add months
            $base = max(strtotime($member['end_date']), strtotime(date('Y-m-d')));
            $new_end = date('Y-m-d', strtotime('+' . $plan['duration_months'] . ' months', $base));

            // update member with plan name and new end_date
            $stmt = $pdo->prepare('UPDATE members SET plan = ?, end_date = ? WHERE id = ?');
            $stmt->execute([$plan_name, $new_end, $member_id]);

            log_action($pdo, 'renew', "Member {$member_id} updated to plan {$plan_name} until {$new_end}");
            header('Location: members.php');
            exit;
        }
    }
}

$csrf = csrf_get_token();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Renew Membership</title><script src="tailwind.min.css"></script></head>
<body class="bg-gray-50 min-h-screen p-6">
  <a href="members.php" class="text-indigo-600">&larr; Back</a>
  <div class="max-w-lg bg-white p-6 rounded shadow mt-4">
    <h2 class="text-lg font-semibold mb-3">Renew Membership</h2>
    <?php if($member): ?>
      <div class="mb-3">
        <div><strong><?php echo e($member['first_name'].' '.$member['last_name']); ?></strong></div>
        <div>Current end: <?php echo e($member['end_date']); ?></div>
        <div>Current plan: <?php echo e($member['plan'] ?? 'None'); ?></div>
      </div>
    <?php endif; ?>

    <?php if($errors): ?>
      <div class="bg-red-50 p-3 mb-3"><?php foreach($errors as $e) echo '<div class="text-red-700">'.e($e).'</div>'; ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-3">
      <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
      <input type="hidden" name="member_id" value="<?php echo e($member_id); ?>">
      <div>
        <label class="block text-sm">Plan</label>
        <select name="plan" class="w-full p-2 border rounded" required>
          <option value="">Select a plan</option>
          <?php foreach($plans as $p): ?>
            <option value="<?php echo e($p['name']); ?>" <?php if($member && $member['plan']==$p['name']) echo 'selected'; ?>>
              <?php echo e($p['name'].' — '.$p['duration_months'].' month(s)'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><button class="bg-green-600 text-white px-3 py-2 rounded">Renew</button></div>
    </form>
  </div>
</body></html>
