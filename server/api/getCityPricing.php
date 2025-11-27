<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

include '../config/db_connect.php'; // adjust path if needed

try {
    $stmt = $pdo->query("SELECT id, place, price FROM delivery_rates ORDER BY place ASC");
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cities);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
