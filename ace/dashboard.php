<?php
require 'config.php';
require 'functions.php';
require_login();

// Stats
$total = $pdo->query('SELECT COUNT(*) FROM members')->fetchColumn();
$active = $pdo->query('SELECT COUNT(*) FROM members WHERE end_date >= CURDATE()')->fetchColumn();
$expired = $pdo->query('SELECT COUNT(*) FROM members WHERE end_date < CURDATE()')->fetchColumn();

$csrf = csrf_get_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Gym Management</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="tailwind.min.css"></script><!-- Local Tailwind CSS -->
</head>
<body class="bg-gray-50 min-h-screen flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-md min-h-screen p-4 flex flex-col">
    <div class="text-2xl font-bold text-indigo-600 mb-6">AceFitness</div>
    <nav class="flex-1 space-y-2">
      <a href="dashboard.php" class="block p-2 rounded hover:bg-indigo-100 font-medium text-indigo-700">Dashboard</a>
      <a href="members.php" class="block p-2 rounded hover:bg-indigo-100">Members</a>
     <!-- <a href="renew.php" class="block p-2 rounded hover:bg-indigo-100">Renew</a>-->
      <a href="reports.php" class="block p-2 rounded hover:bg-indigo-100">Reports</a>
    </nav>
    <a href="logout.php" class="mt-auto block p-2 rounded text-red-600 hover:bg-red-100">
      Logout (<?php echo e($_SESSION['user']['username']); ?>)
    </a>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Total Members</div>
        <div class="text-2xl font-bold"><?php echo (int)$total; ?></div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Active</div>
        <div class="text-2xl font-bold text-green-600"><?php echo (int)$active; ?></div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Expired</div>
        <div class="text-2xl font-bold text-red-600"><?php echo (int)$expired; ?></div>
      </div>
    </div>

    <!-- Quick Check Section -->
    <section class="mt-6">
      <h3 class="font-semibold mb-2">Quick Check</h3>
      <form id="checkForm" class="flex gap-2">
        <input id="q" name="q" placeholder="Enter phone or ID" class="p-2 border rounded flex-1">
        <button type="button" id="checkBtn" class="p-2 bg-indigo-600 text-white rounded">Check</button>
      </form>
      <div id="checkResult" class="mt-3"></div>
    </section>
  </main>

<script>
document.getElementById('checkBtn').addEventListener('click', async function(){
  const q = document.getElementById('q').value.trim();
  if (!q) return alert('Enter phone or ID');
  const res = await fetch('ajax_check.php?q=' + encodeURIComponent(q));
  const data = await res.json();
  const el = document.getElementById('checkResult');
  if (data.error) {
    el.innerHTML = '<div class="text-red-600">'+data.error+'</div>';
  } else {
    el.innerHTML = '<div class="p-3 bg-white rounded shadow"><strong>'+data.name+'</strong><div>Status: '+data.status+'</div><div>Ends: '+data.end_date+'</div></div>';
  }
});
</script>
</body>
</html>
