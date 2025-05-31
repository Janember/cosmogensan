<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include('../config/db_connect.php');

try {
    // Total reservations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reservations");
    $totalReservations = $stmt->fetch()['total'];

    // Status breakdown
    $statuses = ['approved', 'waiting approval', 'confirming payment', 'waiting payment', 'pending', 'rejected'];
    $statusCounts = [];
    foreach ($statuses as $status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = ?");
        $stmt->execute([$status]);
        $statusCounts[str_replace(' ', '_', strtolower($status))] = $stmt->fetch()['count'];
    }

    // Total sales
    $stmt = $pdo->query("SELECT SUM(price) as total_sales FROM reservations WHERE status = 'approved'");
    $totalSales = $stmt->fetch()['total_sales'] ?? 0;

    // Caskets + Chapels
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM caskets");
    $casketCount = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM chapels");
    $chapelCount = $stmt->fetch()['total'];

    echo json_encode([
        'success' => true,
        'total_reservations' => $totalReservations,
        'status_counts' => $statusCounts,
        'total_sales' => (float) $totalSales,
        'casket_chapel_count' => $casketCount + $chapelCount,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
