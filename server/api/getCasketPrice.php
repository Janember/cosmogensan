<?php
// CORS Headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../config/db_connect.php');

$name = $_GET['name'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT package_price FROM caskets WHERE name = ?");
    $stmt->execute([$name]);
    $price = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($price) {
        echo json_encode(['success' => true, 'price' => $price['package_price']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Casket not found.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query failed.']);
}