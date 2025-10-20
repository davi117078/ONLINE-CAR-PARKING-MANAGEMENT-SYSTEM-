<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use TCPDF;

// Get filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Fetch revenue data
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(start_time, '%Y-%m') AS month, 
           SUM(amount_paid) AS total
    FROM bookings
    WHERE status = 'completed' 
      AND start_time BETWEEN ? AND ?
    GROUP BY month
    ORDER BY month ASC
");
$stmt->execute([$start_date, $end_date]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary stats
$total_revenue = array_sum(array_column($data, 'total'));
$total_months = count($data);

// Initialize TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('ParkFinder System');
$pdf->SetAuthor('ParkFinder Admin');
$pdf->SetTitle('Revenue Report');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 12, 'ParkFinder Revenue Report', 0, 1, 'C');
$pdf->Ln(4);

// Date range
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Period: $start_date to $end_date", 0, 1, 'C');
$pdf->Ln(8);

// Summary section
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, "Summary", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 6, "Total Months: $total_months", 0, 1, 'L');
$pdf->Cell(0, 6, "Total Revenue: KES " . number_format($total_revenue, 2), 0, 1, 'L');
$pdf->Ln(8);

// Table header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(0, 123, 255);
$pdf->SetTextColor(255);
$pdf->Cell(40, 10, 'Month', 1, 0, 'C', 1);
$pdf->Cell(60, 10, 'Revenue (KES)', 1, 1, 'C', 1);

// Table rows
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(0);
$fill = 0;
foreach ($data as $row) {
    $pdf->SetFillColor($fill ? 245 : 255);
    $pdf->Cell(40, 10, $row['month'], 1, 0, 'C', 1);
    $pdf->Cell(60, 10, number_format($row['total'], 2), 1, 1, 'C', 1);
    $fill = !$fill;
}

// Footer line
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'R');

// Output PDF
$pdf->Output('revenue_report.pdf', 'D');
exit;
?>
