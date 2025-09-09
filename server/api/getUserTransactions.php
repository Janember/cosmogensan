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
    $userID = $_GET['user_id'];
    $sql = "
        SELECT r.*,
               t.id AS transaction_id,
               t.amount
        FROM reservations r
        LEFT JOIN transactions t ON r.id = t.reservation_id
        WHERE r.user_id = :user_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $results]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
