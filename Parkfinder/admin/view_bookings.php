<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../src/db.php';

// Fetch all bookings with related user and slot info
$stmt = $pdo->query("
    SELECT 
        b.id, 
        b.booking_code, 
        u.name AS user_name, 
        s.slot_name, 
        s.location, 
        b.start_time, 
        b.end_time, 
        b.status, 
        s.hourly_rate AS price_per_hour
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN parking_slots s ON b.slot_id = s.id
    ORDER BY b.id DESC
");
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Bookings - ParkFinder</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
  body{display:flex;min-height:100vh;background:#f2f4f8;color:#333;}
  .sidebar{
    width:250px;background:linear-gradient(180deg,#007bff,#0056b3);
    color:#fff;padding:20px;flex-shrink:0;
  }
  .sidebar h2{text-align:center;margin-bottom:25px;font-size:1.4rem;}
  .sidebar a{
    display:block;color:#fff;text-decoration:none;
    padding:10px;border-radius:6px;margin-bottom:8px;
    transition:background .3s;
  }
  .sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.25);}
  .main{flex:1;padding:25px;}
  .main h3{margin-bottom:15px;}
  .search-box{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
  .search-box input{padding:8px;width:230px;border:1px solid #ccc;border-radius:4px;}
  .search-box button{
    padding:8px 15px;border:none;background:#007bff;color:#fff;
    border-radius:4px;cursor:pointer;transition:background .3s;
  }
  .search-box button:hover{background:#005dc1;}
  .table-container{
    background:#fff;padding:15px;border-radius:8px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);overflow-x:auto;
  }
  table{width:100%;border-collapse:collapse;min-width:800px;}
  thead{background:#007bff;color:#fff;}
  th,td{padding:10px;text-align:left;border-bottom:1px solid #ddd;}
  tr:hover{background:#f9f9f9;}
  .status-badge{
    display:inline-block;padding:5px 10px;font-size:.85rem;
    border-radius:5px;font-weight:500;text-transform:capitalize;
  }
  .status-active{background:#28a745;color:#fff;}
  .status-completed{background:#6c757d;color:#fff;}
  .status-pending{background:#ffc107;color:#000;}
  .status-cancelled{background:#dc3545;color:#fff;}
  @media(max-width:768px){
    body{flex-direction:column;}
    .sidebar{width:100%;text-align:center;}
  }
</style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>ParkFinder Admin</h2>
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="manage_slots.php">🅿️ Manage Slots</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="view_bookings.php" class="active">📋 View Bookings</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../public/logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <h3>All Bookings</h3>
    <form method="GET" class="search-box">
      <input type="text" name="q" placeholder="Search by booking code or user...">
      <button type="submit">Search</button>
    </form>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Booking Code</th>
            <th>User</th>
            <th>Slot</th>
            <th>Location</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Rate (KES/h)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php if(count($bookings)>0): foreach($bookings as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['id']) ?></td>
            <td><strong><?= htmlspecialchars($b['booking_code']) ?></strong></td>
            <td><?= htmlspecialchars($b['user_name']) ?></td>
            <td><?= htmlspecialchars($b['slot_name']) ?></td>
            <td><?= htmlspecialchars($b['location']) ?></td>
            <td><?= htmlspecialchars($b['start_time']) ?></td>
            <td><?= htmlspecialchars($b['end_time']) ?></td>
            <td><?= htmlspecialchars($b['price_per_hour']) ?></td>
            <td>
              <?php
              $status=strtolower($b['status']);
              $class=match($status){
                'active'=>'status-active',
                'completed'=>'status-completed',
                'pending'=>'status-pending',
                'cancelled'=>'status-cancelled',
                default=>''
              };
              ?>
              <span class="status-badge <?= $class ?>"><?= ucfirst($status) ?></span>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="9" style="text-align:center;color:#777;">No bookings found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
