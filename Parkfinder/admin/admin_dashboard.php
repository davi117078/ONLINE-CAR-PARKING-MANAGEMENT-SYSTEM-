<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../src/db.php';

// Quick stats
$totalSlots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();
$vacantSlots = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE status='vacant'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Recent bookings
$bookings = $pdo->query("
    SELECT b.id, b.booking_code, u.name AS user_name, s.slot_name, b.start_time, b.end_time, b.status
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN parking_slots s ON b.slot_id = s.id
    ORDER BY b.id DESC LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - ParkFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      min-height: 100vh;
      background: #f1f5f9;
      font-family: 'Poppins', sans-serif;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #007bff, #004b9b);
      color: #fff;
      padding: 20px;
      flex-shrink: 0;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 1.4rem;
      margin-bottom: 25px;
    }
    .sidebar a {
      display: block;
      color: #fff;
      padding: 10px 12px;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 8px;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
    }

    /* Main */
    .main {
      flex: 1;
      padding: 30px;
    }
    .page-title {
      font-weight: 600;
      margin-bottom: 25px;
    }

    /* Stats */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.2rem;
      margin-bottom: 25px;
    }
    .stat-card {
      background: #fff;
      border-radius: 14px;
      padding: 20px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }
    .stat-icon {
      font-size: 1.8rem;
      color: #007bff;
    }
    .stat-card h6 {
      color: #666;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    .stat-card h2 {
      font-size: 2rem;
      margin-top: 6px;
      color: #000;
    }

    /* Table */
    .card {
      border: none;
      border-radius: 14px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .table thead {
      background-color: #007bff;
      color: #fff;
    }
    .table-hover tbody tr:hover {
      background-color: #f2f6fc;
    }
    .badge {
      font-size: 0.8rem;
      padding: 6px 10px;
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>ParkFinder Admin</h2>
    <a href="admin_dashboard.php" class="active">🏠 Dashboard</a>
    <a href="manage_slots.php">🅿️ Manage Slots</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="view_bookings.php">📋 View Bookings</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../public/logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="page-title"><i class="fa-solid fa-gauge-high me-2 text-primary"></i>Admin Dashboard</h3>
      <span class="text-secondary"><?= date('l, F j, Y'); ?></span>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <i class="fa-solid fa-square-parking stat-icon"></i>
        <h6>Total Slots</h6>
        <h2><?= htmlspecialchars($totalSlots) ?></h2>
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-parking stat-icon"></i>
        <h6>Vacant Slots</h6>
        <h2><?= htmlspecialchars($vacantSlots) ?></h2>
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-users stat-icon"></i>
        <h6>Total Users</h6>
        <h2><?= htmlspecialchars($totalUsers) ?></h2>
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-book stat-icon"></i>
        <h6>Total Bookings</h6>
        <h2><?= htmlspecialchars($totalBookings) ?></h2>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card p-4">
      <h5 class="mb-3"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Recent Bookings</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Booking Code</th>
              <th>User</th>
              <th>Slot</th>
              <th>Start</th>
              <th>End</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['id']) ?></td>
                <td><?= htmlspecialchars($b['booking_code']) ?></td>
                <td><?= htmlspecialchars($b['user_name']) ?></td>
                <td><?= htmlspecialchars($b['slot_name']) ?></td>
                <td><?= htmlspecialchars($b['start_time']) ?></td>
                <td><?= htmlspecialchars($b['end_time']) ?></td>
                <td>
                  <?php
                    $badgeClass = match($b['status']) {
                      'active' => 'bg-success',
                      'completed' => 'bg-secondary',
                      default => 'bg-warning text-dark'
                    };
                  ?>
                  <span class="badge <?= $badgeClass ?>">
                    <?= htmlspecialchars(ucfirst($b['status'])) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
