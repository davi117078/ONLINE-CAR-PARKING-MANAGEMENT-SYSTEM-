<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../src/db.php';

// Handle filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Fetch summary stats
$total_slots = $pdo->query("SELECT COUNT(*) FROM parking_slots")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE start_time BETWEEN ? AND ?");
$stmt->execute([$start_date, $end_date]);
$total_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(amount_paid) FROM bookings WHERE status='completed' AND start_time BETWEEN ? AND ?");
$stmt->execute([$start_date, $end_date]);
$total_revenue = $stmt->fetchColumn() ?? 0;

// Fetch monthly revenue data
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(start_time, '%Y-%m') AS month, SUM(amount_paid) AS total
    FROM bookings
    WHERE status='completed' AND start_time BETWEEN ? AND ?
    GROUP BY month ORDER BY month ASC
");
$stmt->execute([$start_date, $end_date]);
$chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$months = array_column($chartData, 'month');
$totals = array_column($chartData, 'total');

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="revenue_report.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Month', 'Revenue']);
    foreach ($chartData as $row) fputcsv($out, $row);
    fclose($out);
    exit;
}

// PDF Export using TCPDF
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    require_once __DIR__ . '/../tcpdf/tcpdf.php';

    $pdf = new TCPDF();
    $pdf->SetCreator('ParkFinder System');
    $pdf->SetAuthor('ParkFinder');
    $pdf->SetTitle('Revenue Report');
    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'ParkFinder Revenue Report', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 12);
    $pdf->Write(0, "Period: $start_date to $end_date\n\n");

    $html = '
    <table border="1" cellspacing="0" cellpadding="6">
        <tr style="background-color:#007bff;color:#fff;">
            <th><b>Month</b></th>
            <th><b>Revenue (KES)</b></th>
        </tr>';
    foreach ($chartData as $r) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($r['month']) . '</td>
                    <td>' . number_format($r['total'], 2) . '</td>
                  </tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Ln(10);

    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'R');

    $pdf->Output('revenue_report.pdf', 'D');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports & Analytics - ParkFinder</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
  background: linear-gradient(180deg, #007bff, #004c97);
  color: #fff;
  flex-shrink: 0;
  padding: 20px;
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
  border-radius: 8px;
  text-decoration: none;
  transition: background .3s;
}
.sidebar a:hover, .sidebar a.active {
  background: rgba(255,255,255,0.2);
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

/* Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 1.2rem;
}
.card {
  background: #fff;
  border: none;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  transition: transform .2s, box-shadow .2s;
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 14px rgba(0,0,0,0.15);
}
.card h6 {
  color: #777;
  font-size: 0.9rem;
}
.card h2 {
  margin-top: 8px;
  font-size: 1.8rem;
  font-weight: 600;
}
.icon {
  font-size: 1.6rem;
  color: #007bff;
}

/* Filter */
.filter-box {
  background: #fff;
  padding: 1rem;
  border-radius: 14px;
  margin-bottom: 1.5rem;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

/* Chart */
.chart-container {
  background: #fff;
  padding: 1.5rem;
  border-radius: 14px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  margin-top: 1.5rem;
}

.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #fff !important;
}
</style>
</head>
<body>

<div class="sidebar">
  <h2>ParkFinder Admin</h2>
  <a href="admin_dashboard.php">🏠 Dashboard</a>
  <a href="manage_slots.php">🅿️ Manage Slots</a>
  <a href="manage_users.php">👥 Manage Users</a>
  <a href="view_bookings.php">📋 View Bookings</a>
  <a href="reports.php" class="active">📊 Reports</a>
  <a href="../public/logout.php">🚪 Logout</a>
</div>

<div class="main">
  <h3 class="page-title"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Reports & Analytics</h3>

  <!-- Filter Section -->
  <div class="filter-box mb-4">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
      </div>
      <div class="col-md-3">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill"><i class="fa fa-search me-1"></i>Filter</button>
        <a href="?export=pdf&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-danger btn-export flex-fill"><i class="fa fa-file-pdf"></i>PDF</a>
        <a href="?export=csv&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-success btn-export flex-fill"><i class="fa fa-file-csv"></i>CSV</a>
      </div>
    </form>
  </div>

  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="card">
      <div class="icon mb-2"><i class="fa-solid fa-square-parking"></i></div>
      <h6>Total Slots</h6>
      <h2><?= htmlspecialchars($total_slots) ?></h2>
    </div>
    <div class="card">
      <div class="icon mb-2"><i class="fa-solid fa-users"></i></div>
      <h6>Total Users</h6>
      <h2><?= htmlspecialchars($total_users) ?></h2>
    </div>
    <div class="card">
      <div class="icon mb-2"><i class="fa-solid fa-book"></i></div>
      <h6>Total Bookings</h6>
      <h2><?= htmlspecialchars($total_bookings) ?></h2>
    </div>
    <div class="card">
      <div class="icon mb-2"><i class="fa-solid fa-coins"></i></div>
      <h6>Total Revenue (KES)</h6>
      <h2><?= number_format($total_revenue, 2) ?></h2>
    </div>
  </div>

  <!-- Chart Section -->
  <div class="chart-container">
    <h5 class="mb-3"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Monthly Revenue Overview</h5>
    <canvas id="revenueChart" height="100"></canvas>
  </div>
</div>

<script>
const ctx = document.getElementById('revenueChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($months) ?>,
    datasets: [{
      label: 'Revenue (KES)',
      data: <?= json_encode($totals) ?>,
      backgroundColor: 'rgba(0,123,255,0.7)',
      borderColor: '#007bff',
      borderWidth: 1,
      borderRadius: 8
    }, {
      type: 'line',
      label: 'Trend',
      data: <?= json_encode($totals) ?>,
      borderColor: '#28a745',
      fill: false,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: true } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
