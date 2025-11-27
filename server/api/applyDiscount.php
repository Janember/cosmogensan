<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reservation_id']) || !isset($data['new_price'])) {
    echo json_encode(['success' => false, 'message' => 'Missing reservation_id or new_price']);
    exit;
}

$reservationId = $data['reservation_id'];
$newPrice = $data['new_price'];

try {
    $stmt = $pdo->prepare("
        UPDATE reservations 
        SET price = :new_price, rem_balance = :new_rem_balance, discounted = 1 
        WHERE id = :id
    ");
    $stmt->execute([
        ':new_price' => $newPrice,
        ':new_rem_balance' => $newPrice,
        ':id' => $reservationId
    ]);

    echo json_encode(['success' => true, 'new_price' => $newPrice]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
