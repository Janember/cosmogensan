<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");
require_once '../config/db_connect.php';

$reservationId = $_GET['id'] ?? null;

if (!$reservationId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM transactions WHERE id = ?");
$stmt->execute([$reservationId]);
$reservation = $stmt->fetch();

if (!$reservation) {
    http_response_code(404);
    echo json_encode(['error' => 'Reservation not found']);
    exit;
}

echo json_encode(['status' => $reservation['status']]);
