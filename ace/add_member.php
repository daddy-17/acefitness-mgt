<?php
require 'config.php';
require 'functions.php';
require_login();
csrf_check();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $start = $_POST['start_date'] ?? date('Y-m-d');
    $plan = $_POST['plan'] ?? '1'; // default 1 month plan

    // Calculate end date based on plan
    $end = date('Y-m-d', strtotime($start . " +{$plan} months"));

    // validate
    if ($first === '' || $last === '' || $phone === '') {
        $errors[] = 'First name, last name, and phone are required';
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO members (first_name, last_name, email, phone, start_date, end_date, plan) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$first, $last, $email, $phone, $start, $end, $plan]);

        log_action($pdo, 'add_member', "Added member {$first} {$last} with {$plan}-month plan");
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
    <title>Add Member</title>
    <script src="tailwind.min.css"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <a href="members.php" class="text-indigo-600">&larr; Back to members</a>
    <div class="max-w-lg bg-white p-6 rounded shadow mt-4">
        <h2 class="text-lg font-semibold mb-3">Add Member</h2>
        <?php if($errors): ?>
            <div class="bg-red-50 p-3 mb-3">
                <?php foreach($errors as $e) echo '<div class="text-red-700">'.e($e).'</div>'; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
            <div>
                <label class="block text-sm">First name</label>
                <input name="first_name" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm">Last name</label>
                <input name="last_name" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm">Phone</label>
                <input name="phone" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm">Email</label>
                <input name="email" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="w-full p-2 border rounded" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div>
                <label class="block text-sm">Plan Duration</label>
                <select name="plan" id="plan" class="w-full p-2 border rounded">
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">12 Months</option>
                </select>
            </div>
            <div>
                <label class="block text-sm">End Date</label>
                <input type="date" name="end_date" id="end_date" class="w-full p-2 border rounded" readonly>
            </div>
            <div>
                <button class="bg-green-600 text-white px-3 py-2 rounded">Save Member</button>
            </div>
        </form>
    </div>

<script>
// Auto-calculate end date when plan changes
const startInput = document.getElementById('start_date');
const planInput = document.getElementById('plan');
const endInput = document.getElementById('end_date');

function updateEndDate() {
    const startDate = new Date(startInput.value);
    const months = parseInt(planInput.value);
    if (!isNaN(startDate) && !isNaN(months)) {
        startDate.setMonth(startDate.getMonth() + months);
        const endDate = startDate.toISOString().split('T')[0];
        endInput.value = endDate;
    }
}

startInput.addEventListener('change', updateEndDate);
planInput.addEventListener('change', updateEndDate);

// Set initial end date
updateEndDate();
</script>
</body>
</html>
