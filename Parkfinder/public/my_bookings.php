<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('driver');
require_once __DIR__ . '/../src/db.php';

$user = current_user();

// Handle booking cancellation
if (isset($_GET['cancel'])) {
    $booking_id = intval($_GET['cancel']);
    $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status IN ('pending','reserved')");
    $stmt->execute([$booking_id, $user['id']]);
}

// Fetch user's bookings
$stmt = $pdo->prepare("
    SELECT 
        b.id, 
        s.slot_name, 
        br.name AS branch_name, 
        b.start_time AS booking_time, 
        b.end_time, 
        TIMESTAMPDIFF(HOUR, b.start_time, b.end_time) AS duration_hours, 
        b.amount_paid AS total_cost, 
        b.status
    FROM bookings b
    JOIN parking_slots s ON b.slot_id = s.id
    JOIN branches br ON s.branch_id = br.id
    WHERE b.user_id = ?
    ORDER BY b.start_time DESC
");
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings - ParkFinder</title>
<link rel="stylesheet" href="../public/assets/css/common.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f2f4f8;
  margin: 0;
  display: flex;
  min-height: 100vh;
  color: #333;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background: linear-gradient(180deg, #0d6efd, #003c9e);
  color: #fff;
  padding: 20px;
  flex-shrink: 0;
}
.sidebar h2 {
  text-align: center;
  font-size: 1.4rem;
  margin-bottom: 30px;
}
.sidebar a {
  display: block;
  color: #fff;
  text-decoration: none;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 8px;
  transition: background 0.3s;
}
.sidebar a:hover,
.sidebar a.active {
  background: rgba(255,255,255,0.25);
}

/* Main */
.main {
  flex-grow: 1;
  padding: 25px;
}
.main h3 {
  margin-bottom: 20px;
}
.table-container {
  background: #fff;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  min-width: 900px;
}
th, td {
  padding: 10px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}
thead {
  background: #0d6efd;
  color: #fff;
}
tr:hover {
  background: #f9f9f9;
}
.badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 0.85rem;
  text-transform: capitalize;
  font-weight: 500;
}
.bg-primary { background: #0d6efd; color: #fff; }
.bg-success { background: #28a745; color: #fff; }
.bg-secondary { background: #6c757d; color: #fff; }
.bg-danger { background: #dc3545; color: #fff; }
.bg-warning { background: #ffc107; color: #000; }

.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
}
.btn-danger {
  background: #dc3545;
  color: #fff;
}
.btn-danger:hover {
  background: #b02a37;
}
.text-muted {
  color: #777;
}

/* Responsive */
@media (max-width: 768px) {
  body {
    flex-direction: column;
  }
  .sidebar {
    width: 100%;
    text-align: center;
  }
}
</style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fa-solid fa-square-parking"></i> ParkFinder</h2>
    <a href="driver_dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i> Dashboard</a>
    <a href="search.php"><i class="fa-solid fa-magnifying-glass me-2"></i> Search Parking</a>
    <a href="my_bookings.php" class="active"><i class="fa-solid fa-book me-2"></i> My Bookings</a>
    <a href="reserve.php"><i class="fa-solid fa-car me-2"></i> Active Reservations</a>
    <a href="../src/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <h3><i class="fa-solid fa-book me-2"></i> My Bookings</h3>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Slot</th>
            <th>Branch</th>
            <th>Booking Time</th>
            <th>Duration (hrs)</th>
            <th>Total (KES)</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($bookings): $i=1; foreach ($bookings as $b): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($b['slot_name']) ?></td>
            <td><?= htmlspecialchars($b['branch_name']) ?></td>
            <td><?= date("d M Y, h:i A", strtotime($b['booking_time'])) ?></td>
            <td><?= $b['duration_hours'] ?></td>
            <td><?= number_format($b['total_cost'], 2) ?></td>
            <td>
              <?php
              $class = match ($b['status']) {
                'reserved' => 'bg-primary',
                'checked_in' => 'bg-success',
                'checked_out' => 'bg-secondary',
                'cancelled' => 'bg-danger',
                default => 'bg-warning'
              };
              ?>
              <span class="badge <?= $class ?>"><?= ucfirst($b['status']) ?></span>
            </td>
            <td>
              <?php if (in_array($b['status'], ['reserved', 'pending'])): ?>
                <a href="?cancel=<?= $b['id'] ?>" class="btn btn-danger" onclick="return confirm('Cancel this booking?');">
                  <i class="fa-solid fa-xmark"></i> Cancel
                </a>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="8" class="text-muted">You have no bookings yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
