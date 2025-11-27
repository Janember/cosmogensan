<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include('../config/db_connect.php');

try {
    $stmt = $pdo->query("SELECT place, price FROM delivery_rates ORDER BY place ASC");
    $deliveryRates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $deliveryRates
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => "Database error: " . $e->getMessage()
    ]);
}
