<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('driver');
require_once __DIR__ . '/../src/db.php';

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['location'])) {
  $location = '%' . trim($_GET['location']) . '%';
  $max_price = !empty($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
  $vehicle_type = !empty($_GET['vehicle_type']) ? trim($_GET['vehicle_type']) : 'car';

  $stmt = $pdo->prepare("SELECT * FROM parking_slots 
        WHERE location LIKE ? 
        AND hourly_rate <= ? 
        AND status = 'vacant'
        AND (type = ? OR ? = 'any')
        ORDER BY hourly_rate ASC");
  $stmt->execute([$location, $max_price, $vehicle_type, $vehicle_type]);
  $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Parking - ParkFinder</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
  body{display:flex;min-height:100vh;background:#f2f4f8;color:#333;}
  .sidebar{
    width:250px;background:linear-gradient(180deg,#0d6efd,#003c9e);
    color:#fff;padding:20px;flex-shrink:0;
  }
  .sidebar h2{text-align:center;font-size:1.3rem;margin-bottom:25px;}
  .sidebar a{
    display:block;color:#fff;text-decoration:none;
    padding:10px;border-radius:6px;margin-bottom:6px;
    transition:background .3s;
  }
  .sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.25);}
  .main{flex:1;padding:25px;}
  h3{margin-bottom:20px;}
  form{
    background:#fff;padding:20px;border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);margin-bottom:20px;
  }
  label{display:block;font-weight:500;margin-bottom:6px;}
  input[type="text"],input[type="number"],select{
    width:100%;padding:8px 10px;border:1px solid #ccc;
    border-radius:6px;margin-bottom:15px;font-size:15px;
  }
  button{
    background:#007bff;color:#fff;border:none;
    padding:10px 18px;border-radius:8px;cursor:pointer;
    transition:background .3s;
  }
  button:hover{background:#005dc1;}
  .results h5{margin-bottom:10px;}
  .slot-card{
    background:#fff;border:1px solid #ddd;border-radius:10px;
    padding:15px;margin-bottom:10px;
    display:flex;justify-content:space-between;align-items:center;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
    transition:transform .2s;
  }
  .slot-card:hover{transform:scale(1.01);}
  .slot-card h5{color:#0d6efd;margin-bottom:5px;}
  .slot-card p{margin-bottom:5px;font-size:14px;}
  .reserve-btn{
    background:#28a745;color:#fff;text-decoration:none;
    padding:8px 12px;border-radius:8px;font-size:14px;
  }
  .reserve-btn:hover{background:#218838;}
  .alert{
    padding:12px;background:#ffeeba;color:#856404;
    border-radius:8px;margin-top:10px;
  }
  @media(max-width:768px){
    body{flex-direction:column;}
    .sidebar{width:100%;text-align:center;}
    .slot-card{flex-direction:column;align-items:flex-start;}
  }
</style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fa-solid fa-square-parking"></i> ParkFinder</h2>
    <a href="driver_dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="search.php" class="active"><i class="fa-solid fa-magnifying-glass"></i> Search Parking</a>
    <a href="my_bookings.php"><i class="fa-solid fa-book"></i> My Bookings</a>
    <a href="reserve.php"><i class="fa-solid fa-car"></i> Active Reservations</a>
    <a href="../src/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <h3><i class="fa-solid fa-magnifying-glass"></i> Find Available Parking</h3>

    <form method="GET">
      <div class="form-row">
        <label><i class="fa-solid fa-location-dot"></i> Location</label>
        <input type="text" name="location" placeholder="Enter location..." required>

        <label><i class="fa-solid fa-car"></i> Vehicle Type</label>
        <select name="vehicle_type">
          <option value="car">Car</option>
          <option value="motorcycle">Motorcycle</option>
          <option value="truck">Truck</option>
          <option value="any">Any</option>
        </select>

        <label><i class="fa-solid fa-money-bill"></i> Max Price (KES/hour)</label>
        <input type="number" name="max_price" placeholder="Optional">
      </div>
      <button type="submit"><i class="fa-solid fa-search"></i> Search</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['location'])): ?>
      <div class="results">
        <h5><i class="fa-solid fa-list"></i> Search Results</h5>
        <?php if (count($results) > 0): ?>
          <?php foreach ($results as $slot): ?>
            <div class="slot-card">
              <div>
                <h5><i class="fa-solid fa-square-parking"></i> <?= htmlspecialchars($slot['slot_name']) ?></h5>
                <p><i class="fa-solid fa-location-dot" style="color:red;"></i> <?= htmlspecialchars($slot['location']) ?></p>
                <p><i class="fa-solid fa-money-bill" style="color:green;"></i> <?= htmlspecialchars($slot['hourly_rate']) ?> KES/hour</p>
              </div>
              <a href="reserve.php?slot_id=<?= $slot['id'] ?>" class="reserve-btn">
                <i class="fa-solid fa-circle-check"></i> Reserve
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert">No available slots found for your search criteria.</div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
