<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('attendant');
require_once __DIR__ . '/../src/db.php';

// Handle search & filter
$where = [];
$params = [];

if (!empty($_GET['search'])) {
  $search = "%" . trim($_GET['search']) . "%";
  $where[] = "(b.booking_code LIKE ? OR s.slot_name LIKE ? OR b.vehicle_no LIKE ?)";
  $params = array_merge($params, [$search, $search, $search]);
}

if (!empty($_GET['status'])) {
  $status = trim($_GET['status']);
  $where[] = "b.status = ?";
  $params[] = $status;
}

$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare("
  SELECT b.*, s.slot_name, s.location 
  FROM bookings b
  JOIN parking_slots s ON b.slot_id = s.id
  $where_sql
  ORDER BY b.created_at DESC
");
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Bookings - ParkFinder</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #0062ff, #003c9e);
      color: #fff;
      padding: 20px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar h2 {
      text-align: center;
      font-size: 1.4rem;
      margin-bottom: 30px;
    }

    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 12px;
      border-radius: 6px;
      margin-bottom: 6px;
      transition: background 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
    }

    /* Main */
    .main {
      flex-grow: 1;
      padding: 30px;
    }

    h3 {
      font-size: 1.5rem;
      color: #003c9e;
      margin-bottom: 15px;
    }

    .summary {
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
    }

    /* Filter Bar */
    form {
      background: #fff;
      border-radius: 10px;
      padding: 15px 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
      justify-content: space-between;
    }

    input[type="text"],
    select {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }

    button {
      background-color: #0062ff;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }

    button:hover {
      background-color: #004bb5;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    thead {
      background-color: #0062ff;
      color: white;
    }

    th,
    td {
      padding: 10px 12px;
      border: 1px solid #e0e0e0;
      text-align: center;
      font-size: 0.95rem;
    }

    tbody tr:hover {
      background-color: #f1f5ff;
    }

    .text-muted {
      color: #888;
    }

    /* Status Tags */
    .status {
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-block;
    }

    .status.pending {
      background-color: #ffc107;
      color: #000;
    }

    .status.checked_in {
      background-color: #17a2b8;
      color: white;
    }

    .status.completed {
      background-color: #28a745;
      color: white;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
        padding: 10px;
      }

      .main {
        padding: 15px;
      }

      table,
      thead,
      tbody,
      th,
      td,
      tr {
        display: block;
      }

      thead {
        display: none;
      }

      tr {
        margin-bottom: 12px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
      }

      td {
        border: none;
        text-align: left;
        padding: 6px 0;
      }

      td::before {
        content: attr(data-label);
        font-weight: 600;
        display: block;
        color: #333;
      }
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <h2><i class="fa-solid fa-square-parking"></i> ParkFinder</h2>
      <a href="attendant_dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i> Dashboard</a>
      <a href="checkin.php"><i class="fa-solid fa-sign-in-alt me-2"></i> Check In</a>
      <a href="checkout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Check Out</a>
      <a href="view_bookings.php" class="active"><i class="fa-solid fa-list me-2"></i> Bookings</a>
    </div>
    <a href="../public/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <h3><i class="fa-solid fa-list me-2"></i>All Bookings</h3>
    <div class="summary">Total Bookings: <strong><?= count($bookings) ?></strong></div>

    <!-- Filter Bar -->
    <form method="GET">
      <div class="filter-bar">
        <input type="text" name="search" placeholder="Search booking code, slot, or vehicle"
          value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <select name="status">
          <option value="">All Status</option>
          <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
          <option value="checked_in" <?= (($_GET['status'] ?? '') === 'checked_in') ? 'selected' : '' ?>>Checked In</option>
          <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
        </select>
        <button type="submit"><i class="fa-solid fa-search me-1"></i> Filter</button>
      </div>
    </form>

    <!-- Table -->
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Booking Code</th>
            <th>Slot</th>
            <th>Location</th>
            <th>Vehicle</th>
            <th>Check-in Time</th>
            <th>Check-out Time</th>
            <th>Total Fee (KES)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($bookings) > 0): ?>
            <?php $i = 1;
            foreach ($bookings as $b): ?>
              <tr>
                <td data-label="#"> <?= $i++ ?></td>
                <td data-label="Booking Code"><?= htmlspecialchars($b['booking_code']) ?></td>
                <td data-label="Slot"><?= htmlspecialchars($b['slot_name']) ?></td>
                <td data-label="Location"><?= htmlspecialchars($b['location']) ?></td>
                <td data-label="Vehicle"><?= htmlspecialchars($b['vehicle_no']) ?></td>
                <td data-label="Check-in"><?= $b['checkin_time'] ? date('d M Y, h:i A', strtotime($b['checkin_time'])) : '-' ?></td>
                <td data-label="Check-out"><?= $b['checkout_time'] ? date('d M Y, h:i A', strtotime($b['checkout_time'])) : '-' ?></td>
                <td data-label="Total Fee"><?= $b['total_fee'] ? number_format($b['total_fee'], 2) : '-' ?></td>
                <td data-label="Status">
                  <?php
                  $status_class = $b['status'] === 'completed'
                    ? 'completed'
                    : ($b['status'] === 'checked_in' ? 'checked_in' : 'pending');
                  ?>
                  <span class="status <?= $status_class ?>">
                    <?= ucfirst(str_replace('_', ' ', $b['status'])) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-muted">No bookings found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
