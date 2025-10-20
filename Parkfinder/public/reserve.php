<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('driver');
require_once __DIR__ . '/../src/db.php';

$user = current_user();
$slot_id = isset($_GET['slot_id']) ? intval($_GET['slot_id']) : 0;
$message = '';
$slot = null;

if ($slot_id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM parking_slots WHERE id = ?");
  $stmt->execute([$slot_id]);
  $slot = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $slot_id = intval($_POST['slot_id']);
  $vehicle_no = trim($_POST['vehicle_no']);
  $vehicle_type = trim($_POST['vehicle_type']);
  $start_time = $_POST['start_time'];
  $end_time = $_POST['end_time'];

  if ($slot_id && $vehicle_no && $start_time && $end_time) {
    $booking_code = strtoupper('BK' . rand(10000, 99999));

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, slot_id, vehicle_no, vehicle_type, start_time, end_time, booking_code, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'reserved')");
    $stmt->execute([$user['id'], $slot_id, $vehicle_no, $vehicle_type, $start_time, $end_time, $booking_code]);

    $pdo->prepare("UPDATE parking_slots SET status = 'reserved' WHERE id = ?")->execute([$slot_id]);

    $message = "Reservation successful! Your booking code is <strong>$booking_code</strong>.";
  } else {
    $message = "Please fill all required fields.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve Slot - ParkFinder</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
      min-height: 100vh;
      display: flex;
    }

    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #0062ff, #003d99);
      color: #fff;
      padding: 20px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar h2 {
      font-size: 1.4rem;
      text-align: center;
      margin-bottom: 2rem;
    }

    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 12px;
      border-radius: 6px;
      margin-bottom: 5px;
      transition: background 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .main {
      flex-grow: 1;
      padding: 30px;
    }

    h3 {
      color: #003c9e;
      margin-bottom: 20px;
      font-size: 1.5rem;
    }

    .card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 6px;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    button {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #218838;
      transform: translateY(-1px);
    }

    .alert {
      padding: 10px 15px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 0.95rem;
    }

    .alert-info {
      background-color: #d1ecf1;
      color: #0c5460;
      border: 1px solid #bee5eb;
    }

    .alert-warning {
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }

    .text-primary {
      color: #007bff;
    }

    .text-danger {
      color: #dc3545;
    }

    .text-success {
      color: #28a745;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        flex-direction: row;
        justify-content: space-around;
      }

      .main {
        padding: 15px;
      }
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <div>
      <h2><i class="fa-solid fa-square-parking"></i> ParkFinder</h2>
      <a href="driver_dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i> Dashboard</a>
      <a href="search.php"><i class="fa-solid fa-magnifying-glass me-2"></i> Search Parking</a>
      <a href="my_bookings.php"><i class="fa-solid fa-book me-2"></i> My Bookings</a>
      <a href="reserve.php" class="active"><i class="fa-solid fa-car me-2"></i> Active Reservations</a>
    </div>
    <a href="../src/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
  </div>

  <div class="main">
    <h3><i class="fa-solid fa-circle-check me-2"></i> Reserve Parking Slot</h3>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($slot): ?>
      <div class="card">
        <h4><i class="fa-solid fa-square-parking text-primary me-2"></i><?= htmlspecialchars($slot['slot_name']) ?></h4>
        <p><i class="fa-solid fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($slot['location']) ?></p>
        <p><i class="fa-solid fa-money-bill text-success me-1"></i> <?= htmlspecialchars($slot['hourly_rate']) ?> KES/hour</p>
      </div>

      <form method="POST" class="card">
        <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">

        <div style="display: flex; flex-wrap: wrap; gap: 15px;">
          <div style="flex: 1 1 45%;">
            <label><i class="fa-solid fa-car me-1"></i> Vehicle Number</label>
            <input type="text" name="vehicle_no" required placeholder="e.g., KDA 456X">
          </div>
          <div style="flex: 1 1 45%;">
            <label><i class="fa-solid fa-truck me-1"></i> Vehicle Type</label>
            <select name="vehicle_type" required>
              <option value="car">Car</option>
              <option value="motorcycle">Motorcycle</option>
              <option value="truck">Truck</option>
            </select>
          </div>
          <div style="flex: 1 1 45%;">
            <label><i class="fa-solid fa-clock me-1"></i> Start Time</label>
            <input type="datetime-local" name="start_time" required>
          </div>
          <div style="flex: 1 1 45%;">
            <label><i class="fa-solid fa-clock-rotate-left me-1"></i> End Time</label>
            <input type="datetime-local" name="end_time" required>
          </div>
        </div>

        <div style="text-align: right; margin-top: 20px;">
          <button type="submit"><i class="fa-solid fa-check-circle me-1"></i> Confirm Reservation</button>
        </div>
      </form>
    <?php else: ?>
      <div class="alert alert-warning">Invalid slot selected. Please go back to <a href="search.php">search</a>.</div>
    <?php endif; ?>
  </div>

</body>
</html>
