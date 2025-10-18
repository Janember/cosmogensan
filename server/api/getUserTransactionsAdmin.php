<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "
        SELECT 
            r.*,
            r.id AS reservation_id,
            r.deceased_name,
            r.start_date,
            r.price,
            r.status,
            t.*,
            t.id AS transaction_id,
            t.amount,
            t.status AS transaction_status,
            t.created_at AS transaction_date
        FROM reservations r
        INNER JOIN transactions t ON r.id = t.reservation_id
        ORDER BY r.start_date DESC;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $results]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
