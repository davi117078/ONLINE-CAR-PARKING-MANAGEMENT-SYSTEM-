<?php
require_once __DIR__ . '/../../src/db.php';
header('Content-Type: application/json');
$total = $pdo->query("SELECT COUNT(*) as c FROM parking_slots")->fetchColumn();
$occupied = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE status='occupied'")->fetchColumn();
$reserved = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE status='reserved'")->fetchColumn();
$rev = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success' AND DATE(created_at)=CURDATE()")->fetchColumn();
echo json_encode(['total_slots'=>intval($total),'occupied'=>intval($occupied),'reserved'=>intval($reserved),'revenue_today'=>floatval($rev)]);
