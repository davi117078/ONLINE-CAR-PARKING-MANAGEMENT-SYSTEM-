<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('attendant');
require_once __DIR__ . '/../src/db.php';

$message = '';
$error = '';
$rate_per_hour = 100; // Parking rate

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $booking_code = trim($_POST['booking_code']);

  // Find booking
  $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_code = ?");
  $stmt->execute([$booking_code]);
  $booking = $stmt->fetch();

  if ($booking) {
    if ($booking['status'] === 'pending') {
      $error = "This vehicle has not been checked in yet!";
    } elseif ($booking['status'] === 'completed') {
      $error = "This booking has already been checked out!";
    } else {
      $checkin_time = new DateTime($booking['checkin_time']);
      $checkout_time = new DateTime();
      $interval = $checkin_time->diff($checkout_time);
      $hours = max(1, ceil(($interval->days * 24) + $interval->h + ($interval->i / 60)));

      $total_fee = $hours * $rate_per_hour;

      $update = $pdo->prepare("UPDATE bookings SET status='completed', checkout_time=NOW(), total_fee=? WHERE booking_code=?");
      if ($update->execute([$total_fee, $booking_code])) {
        $message = "✅ Vehicle successfully checked out. Duration: {$hours} hour(s). Total Fee: KES " . number_format($total_fee, 2);
      } else {
        $error = "Failed to update booking. Try again.";
      }
    }
  } else {
    $error = "Booking not found.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check-Out - ParkFinder</title>
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
      display: block;
      color: white;
      text-decoration: none;
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
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .card {
      background: #fff;
      border-radius: 10px;
      padding: 30px 25px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 480px;
    }

    h4 {
      text-align: center;
      color: #003c9e;
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 6px;
      color: #333;
    }

    input[type="text"] {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
      margin-bottom: 15px;
    }

    button {
      background-color: #007bff;
      color: #fff;
      border: none;
      width: 100%;
      padding: 12px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }

    button:hover {
      background-color: #0056b3;
      transform: translateY(-1px);
    }

    .alert {
      border-radius: 6px;
      padding: 12px 15px;
      margin-bottom: 15px;
      text-align: center;
      font-size: 0.95rem;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .text-muted {
      text-align: center;
      color: #666;
      font-size: 0.9rem;
      margin-top: 15px;
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
      <a href="attendant_dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i> Dashboard</a>
      <a href="checkin.php"><i class="fa-solid fa-sign-in-alt me-2"></i> Check In</a>
      <a href="checkout.php" class="active"><i class="fa-solid fa-sign-out-alt me-2"></i> Check Out</a>
      <a href="view_bookings.php"><i class="fa-solid fa-list me-2"></i> Bookings</a>
    </div>
    <a href="../public/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
  </div>

  <div class="main">
    <div class="card">
      <h4><i class="fa-solid fa-sign-out-alt me-2"></i> Vehicle Check-Out</h4>

      <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <label><i class="fa-solid fa-ticket me-1"></i> Booking Code</label>
        <input type="text" name="booking_code" placeholder="Enter booking code" required>
        <button type="submit"><i class="fa-solid fa-check me-1"></i> Confirm Check-Out</button>
      </form>

      <p class="text-muted">
        <i class="fa-solid fa-circle-info me-1"></i> Enter booking code to process vehicle check-out and calculate total parking fee.
      </p>
    </div>
  </div>

</body>
</html>
