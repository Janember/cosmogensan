<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include('../config/db_connect.php');

try {
    // Total reservations (last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reservations WHERE created_at >= NOW() - INTERVAL 30 DAY");
    $totalReservations = $stmt->fetch()['total'];

    // Status breakdown (all-time, keep as is)
    $statuses = ['approved', 'waiting approval', 'confirming payment', 'waiting payment', 'pending', 'rejected'];
    $statusCounts = [];
    foreach ($statuses as $status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = ?");
        $stmt->execute([$status]);
        $statusCounts[str_replace(' ', '_', strtolower($status))] = $stmt->fetch()['count'];
    }

    // Total sales (last 30 days, only approved)
    $stmt = $pdo->query("SELECT SUM(price) as total_sales 
                         FROM reservations 
                         WHERE status = 'approved' 
                         AND created_at >= NOW() - INTERVAL 30 DAY");
    $totalSales = $stmt->fetch()['total_sales'] ?? 0;

    // Caskets (last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reservations WHERE created_at >= NOW() - INTERVAL 30 DAY");
    $casketCount = $stmt->fetch()['total'];

    // Chapels (kept all-time, since you didn’t mention limiting this one)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM chapels");
    $chapelCount = $stmt->fetch()['total'];

    echo json_encode([
        'success' => true,
        'total_reservations' => $totalReservations,
        'status_counts' => $statusCounts,
        'total_sales' => (float) $totalSales,
        'casket_count' => $casketCount,
        'chapel_count' => $chapelCount
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
