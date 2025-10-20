<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../src/db.php';

// Handle adding a slot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slot'])) {
    $branch_id = $_POST['branch_id'];
    $slot_name = trim($_POST['slot_name']);
    $type = $_POST['type'];
    $price = (float) $_POST['price_per_hour'];
    $status = 'vacant';

    $stmt = $pdo->prepare("INSERT INTO parking_slots (branch_id, slot_name, type, hourly_rate, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$branch_id, $slot_name, $type, $price, $status]);
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM parking_slots WHERE id = ?")->execute([$id]);
}

// Fetch data
$branches = $pdo->query("SELECT * FROM branches ORDER BY name ASC")->fetchAll();
$slots = $pdo->query("
    SELECT s.*, b.name 
    FROM parking_slots s 
    JOIN branches b ON s.branch_id = b.id
    ORDER BY s.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Slots - ParkFinder</title>
  <link rel="stylesheet" href="../assets/common.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f6f8;
      margin: 0;
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #007bff, #004b9b);
      color: white;
      padding: 20px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar h2 {
      text-align: center;
      font-size: 1.4rem;
      margin-bottom: 30px;
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 10px 12px;
      border-radius: 6px;
      margin-bottom: 5px;
      transition: 0.3s;
    }

    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.25);
    }

    .main {
      flex: 1;
      padding: 25px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .header h3 {
      margin: 0;
    }

    button, .btn {
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 10px 15px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      background: #0056b3;
    }

    .btn-danger {
      background: #dc3545;
    }

    .btn-success {
      background: #28a745;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      text-align: center;
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background: #007bff;
      color: white;
    }

    tr:hover {
      background: #f1f1f1;
    }

    .badge {
      padding: 4px 10px;
      border-radius: 10px;
      color: white;
      font-size: 0.8rem;
    }

    .bg-success { background: #28a745; }
    .bg-danger { background: #dc3545; }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      border-radius: 10px;
      width: 400px;
      padding: 20px;
      position: relative;
    }

    .modal-content h4 {
      margin-top: 0;
      color: #007bff;
    }

    .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.2rem;
      cursor: pointer;
      color: #555;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .modal-footer {
      text-align: right;
    }

  </style>
</head>

<body>
  <div class="sidebar">
    <h2>ParkFinder Admin</h2>
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="manage_slots.php" class="active">🅿️ Manage Slots</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="view_bookings.php">📋 View Bookings</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../public/logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="header">
      <h3>🅿️ Manage Parking Slots</h3>
      <button onclick="openModal()">➕ Add Slot</button>
    </div>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Slot Name</th>
            <th>Branch</th>
            <th>Type</th>
            <th>Price/hr (KES)</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($slots): $i=1; foreach ($slots as $s): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($s['slot_name']) ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= ucfirst($s['type']) ?></td>
            <td><?= number_format($s['hourly_rate'], 2) ?></td>
            <td>
              <span class="badge <?= $s['status'] === 'vacant' ? 'bg-success' : 'bg-danger' ?>">
                <?= ucfirst($s['status']) ?>
              </span>
            </td>
            <td>
              <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slot?')">🗑 Delete</a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="7">No parking slots found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Slot Modal -->
  <div id="addSlotModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h4>Add New Slot</h4>
      <form method="POST">
        <label>Branch</label>
        <select name="branch_id" required>
          <option value="">Select Branch</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Slot Name</label>
        <input type="text" name="slot_name" required placeholder="E.g. A-12">

        <label>Vehicle Type</label>
        <select name="type" required>
          <option value="car">Car</option>
          <option value="motorcycle">Motorcycle</option>
          <option value="truck">Truck</option>
        </select>

        <label>Price per Hour (KES)</label>
        <input type="number" name="price_per_hour" required min="0" step="0.01">

        <div class="modal-footer">
          <button type="submit" name="add_slot" class="btn btn-success">💾 Save</button>
          <button type="button" class="btn" onclick="closeModal()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('addSlotModal');
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(e) {
      if (e.target == modal) closeModal();
    }
  </script>
</body>
</html>
