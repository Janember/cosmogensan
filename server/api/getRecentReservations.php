<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

require_once('../config/db_connect.php');

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            r.user_name AS customer_name,
            c.name AS casket_name,
            r.start_date,
            r.status,
            r.created_at
        FROM reservations r
        LEFT JOIN caskets c ON r.casket_id = c.id
        ORDER BY r.created_at DESC
        LIMIT 3
    ";

    $stmt = $pdo->query($query);

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $reservations]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}