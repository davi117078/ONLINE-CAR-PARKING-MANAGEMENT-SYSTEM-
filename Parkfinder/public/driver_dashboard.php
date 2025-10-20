<?php
require_once __DIR__ . '/../src/auth.php';
$user = current_user();
if (!$user) header('Location: login.php');

$branches = $pdo->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll();

$notifications = $pdo->prepare("SELECT * FROM notifications WHERE user_role='driver' ORDER BY created_at DESC LIMIT 5");
$notifications->execute();
$notifications = $notifications->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Driver Dashboard - ParkFinder</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      margin: 0;
      overflow-x: hidden;
      color: #222;
      font-size: 17px;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background: #0d6efd;
      color: white;
      transition: width 0.3s ease;
      z-index: 100;
      padding-top: 10px;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar .brand {
      font-size: 1.6rem;
      font-weight: 700;
      text-align: center;
      padding: 1rem 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      position: relative;
    }

    .toggle-btn {
      background: none;
      border: none;
      color: white;
      font-size: 1.4rem;
      position: absolute;
      right: 15px;
      top: 12px;
      cursor: pointer;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 1rem 0;
    }

    .sidebar ul li {
      padding: 12px 20px;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: white;
      display: flex;
      align-items: center;
      font-size: 1.05rem;
      transition: background 0.2s;
      border-radius: 6px;
      padding: 8px;
    }

    .sidebar ul li a:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .sidebar ul li a i {
      margin-right: 12px;
      font-size: 1.3rem;
    }

    .sidebar.collapsed ul li a span {
      display: none;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s ease;
    }

    .sidebar.collapsed~.content {
      margin-left: 70px;
    }

    h3 {
      font-size: 1.7rem;
      margin-bottom: 1.5rem;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
    }

    .col-md-4 {
      flex: 1 1 30%;
      min-width: 300px;
    }

    .col-md-8 {
      flex: 1 1 60%;
      min-width: 350px;
    }

    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
    }

    .card h5 {
      font-size: 1.3rem;
      margin-bottom: 10px;
      color: #0d6efd;
    }

    .btn {
      display: inline-block;
      background: #0d6efd;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 1rem;
      transition: background 0.2s;
    }

    .btn:hover {
      background: #0844a4;
    }

    .btn-success {
      background: #198754;
    }

    .btn-success:hover {
      background: #0e5f3b;
    }

    select,
    input {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .list-group {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .list-group-item {
      border-bottom: 1px solid #eee;
      padding: 12px 0;
    }

    .list-group-item strong {
      color: #333;
      font-size: 1.1rem;
    }

    .alert {
      background: #eaf1ff;
      padding: 10px;
      border-radius: 6px;
      color: #004085;
      font-size: 1rem;
    }

    .text-muted {
      color: #6c757d;
    }

    .text-primary {
      color: #0d6efd;
    }

    .text-center {
      text-align: center;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0;
      }

      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }

      .row {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>

  <div class="sidebar" id="sidebar">
    <div class="brand">
      ParkFinder
      <button class="toggle-btn" id="toggleSidebar"><i class="fa-solid fa-bars"></i></button>
    </div>
    <ul>
      <li><a href="#"><i class="fa-solid fa-gauge"></i><span> Dashboard</span></a></li>
      <li><a href="my_bookings.php"><i class="fa-solid fa-clock-rotate-left"></i><span> My Bookings</span></a></li>
      <li><a href="search.php"><i class="fa-solid fa-magnifying-glass"></i><span> Search Slots</span></a></li>
      <li><a href="reserve.php"><i class="fa-solid fa-car"></i><span> Active Reservations</span></a></li>
      <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span> Logout</span></a></li>
    </ul>
  </div>

  <div class="content">
    <h3>Welcome, <?php echo htmlspecialchars($user['name']); ?></h3>

    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <h5><i class="fa-solid fa-ticket"></i> My Bookings</h5>
          <p>View your booking history and receipts.</p>
          <a href="my_bookings.php" class="btn">View History</a>
        </div>
        <br>

       <div class="card">
          <h5><i class="fa-solid fa-circle-info"></i> Account Info</h5>
          <p><strong>Name: </strong><?= htmlspecialchars($user['name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
          <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <h5><i class="fa-solid fa-magnifying-glass"></i> Quick Search</h5>
          <form id="quickSearch">
            <select id="branch">
              <?php foreach ($branches as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <select id="vtype">
              <option value="car">Car</option>
              <option value="motorcycle">Motorcycle</option>
              <option value="truck">Truck</option>
              <option value="any">Any</option>
            </select>
            <button id="searchBtn" class="btn w-100" type="button">Search Slots</button>
          </form>
          <div id="slotsResult" class="mt-3"></div>
        </div>
      </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
      <h5><i class="fa-solid fa-bell"></i> Announcements</h5>
      <?php if ($notifications): ?>
        <ul class="list-group">
          <?php foreach ($notifications as $n): ?>
            <li class="list-group-item">
              <strong><?= htmlspecialchars($n['title']) ?></strong>
              <p><?= htmlspecialchars($n['message']) ?></p>
              <small class="text-muted"><?= date('d M H:i', strtotime($n['created_at'])) ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No new notifications.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
  </script>
</body>

</html>
