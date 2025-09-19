<?php
require 'config.php';
require 'functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($username === '' || $password === '') {
        $errors[] = 'Please enter username and password';
    } else {
        // Fetch user
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
            log_action($pdo, 'login', 'User '.$user['username'].' logged in');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid credentials';
            log_action($pdo, 'failed_login', 'Attempt for '.$username);
        }
    }
}

$csrf = csrf_get_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Gym Management - Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="tailwind.min.css"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4"><x style="color:red;">Ace</x>Fitness♠ - Login</h1>
    <?php if($errors): ?>
      <div class="bg-red-50 border border-red-200 p-3 mb-4 text-red-700">
        <?php foreach($errors as $e) echo '<div>'.e($e).'</div>'; ?>
      </div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
      <div>
        <label class="block text-sm">Username</label>
        <input name="username" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm">Password</label>
        <input type="password" name="password" class="w-full border rounded p-2" required>
      </div>
      <div>
        <button class="w-full bg-indigo-600 text-white p-2 rounded">Login</button>
      </div>
    </form>
    <p class="text-xs text-gray-500 mt-3">Default admin username: <strong>admin</strong>. After importing DB, update admin password hash in db.sql or create a user via DB.</p>
  </div>
</body>
</html>
