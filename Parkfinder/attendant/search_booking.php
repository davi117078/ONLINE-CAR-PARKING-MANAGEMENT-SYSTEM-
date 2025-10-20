<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('attendant');
require_once __DIR__ . '/../src/db.php';

header('Content-Type: application/json');

$query = trim($_GET['query'] ?? '');

if (empty($query)) {
    echo json_encode(['booking' => null]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        b.*, 
        u.name AS driver_name, 
        u.phone AS driver_phone, 
        s.slot_name, 
        s.location
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN parking_slots s ON b.slot_id = s.id
    WHERE b.booking_code LIKE CONCAT('%', :query, '%') 
       OR b.vehicle_number LIKE CONCAT('%', :query, '%')
    LIMIT 1
");

$stmt->execute(['query' => $query]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['booking' => $booking ?: null]);
