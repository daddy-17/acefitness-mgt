<?php
require 'config.php';
require 'functions.php';
require_login();

$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
list($page, $per_page, $offset) = paginate($page, $per_page);

// Handle search
$search = $_GET['search'] ?? '';
$params = [];
$sql = "SELECT * FROM members WHERE 1=1 ";

if ($search !== '') {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ?) ";
    $s = '%'.$search.'%';
    $params = [$s,$s,$s,$s];
}

// Count total results
$total = $pdo->prepare("SELECT COUNT(*) FROM (" . $sql . ") as t");
$total->execute($params);
$total_count = $total->fetchColumn();

// Add pagination (LIMIT/OFFSET directly as integers)
$per_page = (int)$per_page;
$offset   = (int)$offset;
$sql .= " ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";

// Fetch members
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

$pages = ceil($total_count / $per_page);
$csrf = csrf_get_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Members - Gym Management</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="tailwind.min.css"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <nav class="bg-white shadow p-4 flex justify-between">
    <div class="font-bold">GymMgmt</div>
    <div class="space-x-3">
      <a href="dashboard.php" class="px-3 py-1">Dashboard</a>
      <a href="renew.php" class="px-3 py-1">Renew</a>
      <a href="reports.php" class="px-3 py-1">Reports</a>
      <a href="logout.php" class="px-3 py-1 text-red-600">Logout (<?php echo e($_SESSION['user']['username']); ?>)</a>
    </div>
  </nav>

  <main class="p-6">
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold">Members</h2>
      <a href="add_member.php" class="bg-green-600 text-white px-3 py-1 rounded">Add Member</a>
    </div>

    <div class="mt-4 bg-white p-4 rounded shadow">
      <div class="flex gap-2 mb-4">
        <input id="search" placeholder="Search by name, phone or email" class="flex-1 p-2 border rounded">
        <button id="searchBtn" class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
        <a href="export_csv.php" class="px-3 py-2 bg-slate-600 text-white rounded">Export CSV</a>
        <a href="export_pdf.php" class="px-3 py-2 bg-slate-600 text-white rounded">Printable Report</a>
      </div>

      <div id="membersTable">
        <?php if (!$members): ?>
          <div class="text-gray-500">No members found.</div>
        <?php else: ?>
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

          <!-- Pagination -->
          <div class="mt-4 space-x-1">
            <?php for($i=1;$i<=$pages;$i++): ?>
              <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                 class="px-2 py-1 <?php echo ($i==$page) ? 'bg-indigo-600 text-white rounded' : 'bg-gray-200'; ?>">
                <?php echo $i; ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

<script>
const searchInput = document.getElementById('search');
const searchBtn = document.getElementById('searchBtn');

searchBtn.addEventListener('click', doSearch);
searchInput.addEventListener('keydown', function(e){ if (e.key === 'Enter') doSearch(); });

async function doSearch(){
  const q = searchInput.value.trim();
  window.location.href = '?search=' + encodeURIComponent(q);
}
</script>
</body>
</html>
