<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('attendant');
require_once __DIR__ . '/../src/db.php';

// Dashboard statistics
$total_active = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='active'")->fetchColumn();
$total_completed = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='completed'")->fetchColumn();
$total_today = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(start_time)=CURDATE()")->fetchColumn();
$total_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();

// Recent bookings
$bookings = $pdo->query("
  SELECT b.*, u.name AS driver_name, s.slot_name
  FROM bookings b
  JOIN users u ON b.user_id = u.id
  JOIN parking_slots s ON b.slot_id = s.id
  ORDER BY b.start_time DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attendant Dashboard - ParkFinder</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; display: flex; background-color: #f4f6f8; min-height: 100vh; }

    .sidebar { width: 250px; background: linear-gradient(180deg, #0062ff, #003c9e); color: #fff; padding: 20px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: space-between; }
    .sidebar h2 { text-align: center; margin-bottom: 2rem; font-size: 1.4rem; }
    .sidebar a { display: block; text-decoration: none; color: #fff; padding: 10px 12px; border-radius: 6px; margin-bottom: 6px; transition: background 0.3s; }
    .sidebar a:hover, .sidebar a.active { background-color: rgba(255, 255, 255, 0.2); }

    .main { flex-grow: 1; padding: 30px; }
    h3 { font-size: 1.6rem; color: #003c9e; margin-bottom: 25px; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); padding: 20px; text-align: center; }
    .card h4 { font-size: 0.95rem; color: #6c757d; margin-bottom: 6px; }
    .card h2 { color: #007bff; font-size: 2rem; font-weight: 700; }

    .search-box { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
    .search-box input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }
    .search-box button { background-color: #007bff; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .search-box button:hover { background-color: #0056b3; }

    .alert { padding: 12px; border-radius: 6px; margin-top: 10px; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .alert-danger { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table th, table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
    table th { background-color: #007bff; color: white; font-weight: 600; }
    table tr:hover { background-color: #f1f1f1; }
    .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; color: white; }
    .bg-success { background-color: #28a745; }
    .bg-secondary { background-color: #6c757d; }

    @media (max-width: 768px) {
      body { flex-direction: column; }
      .sidebar { width: 100%; flex-direction: row; justify-content: space-around; }
      .main { padding: 15px; }
      table th, table td { font-size: 0.9rem; }
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div>
      <h2><i class="fa-solid fa-square-parking"></i> ParkFinder</h2>
      <a href="attendant_dashboard.php" class="active"><i class="fa-solid fa-gauge-high me-2"></i> Dashboard</a>
      <a href="checkin.php"><i class="fa-solid fa-sign-in-alt me-2"></i> Check In</a>
      <a href="checkout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Check Out</a>
      <a href="view_bookings.php"><i class="fa-solid fa-list me-2"></i> Bookings</a>
    </div>
    <a href="../public/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
  </div>

  <div class="main">
    <h3><i class="fa-solid fa-user-tie me-2"></i> Attendant Dashboard</h3>

    <div class="stats-grid">
      <div class="card"><h4>Active Bookings</h4><h2><?= htmlspecialchars($total_active) ?></h2></div>
      <div class="card"><h4>Completed Today</h4><h2><?= htmlspecialchars($total_today) ?></h2></div>
      <div class="card"><h4>Total Completed</h4><h2><?= htmlspecialchars($total_completed) ?></h2></div>
      <div class="card"><h4>Total Slots</h4><h2><?= htmlspecialchars($total_slots) ?></h2></div>
    </div>

    <div class="card">
      <h4><i class="fa-solid fa-magnifying-glass me-2"></i> Quick Check Booking</h4>
      <div class="search-box">
        <input type="text" id="searchCode" placeholder="Enter booking code or vehicle number" />
        <button onclick="searchBooking()">Search</button>
      </div>
      <div id="searchResult"></div>
    </div>

    <div class="card" style="margin-top: 20px;">
      <h4><i class="fa-solid fa-clock-rotate-left me-2"></i> Recent Bookings</h4>
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr><th>Booking Code</th><th>Driver</th><th>Slot</th><th>Start</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['booking_code']) ?></td>
                <td><?= htmlspecialchars($b['driver_name']) ?></td>
                <td><?= htmlspecialchars($b['slot_name']) ?></td>
                <td><?= htmlspecialchars($b['start_time']) ?></td>
                <td>
                  <span class="badge <?= $b['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= ucfirst($b['status']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    let typingTimer;
    const delay = 500;

    document.getElementById('searchCode').addEventListener('input', () => {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(searchBooking, delay);
    });

    async function searchBooking() {
      const code = document.getElementById('searchCode').value.trim();
      const out = document.getElementById('searchResult');
      out.innerHTML = '';

      if (!code) return;

      try {
        const res = await fetch('search_booking.php?query=' + encodeURIComponent(code));
        const data = await res.json();

        if (!data || !data.booking) {
          out.innerHTML = '<div class="alert alert-danger">Booking not found</div>';
          return;
        }

        const b = data.booking;
        out.innerHTML = `
          <div class="alert alert-success">
            <strong>Booking Code:</strong> ${b.booking_code}<br>
            <strong>Driver:</strong> ${b.driver_name} (${b.driver_phone || 'N/A'})<br>
            <strong>Slot:</strong> ${b.slot_name}<br>
            <strong>Location:</strong> ${b.location || 'N/A'}<br>
            <strong>Status:</strong> ${b.status}<br>
            <strong>Start Time:</strong> ${b.start_time || 'N/A'}<br>
            <strong>End Time:</strong> ${b.end_time || 'N/A'}
          </div>
        `;
      } catch (err) {
        console.error(err);
        out.innerHTML = '<div class="alert alert-danger">Error fetching booking data</div>';
      }
    }
  </script>
</body>
</html>
