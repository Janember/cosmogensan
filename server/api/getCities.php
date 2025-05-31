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

try {
    $stmt = $pdo->query("SELECT DISTINCT place FROM delivery_rates ORDER BY place ASC");
    $cities = [];

    while ($row = $stmt->fetch()) {
        $cities[] = $row['place'];
    }

    echo json_encode(['success' => true, 'data' => $cities]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query failed.']);
}
