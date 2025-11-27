<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include('../config/db_connect.php');

try {
    $stmt = $pdo->query("SELECT id, package_price FROM caskets ORDER BY id ASC");
    $caskets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $caskets
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => "Database error: " . $e->getMessage()
    ]);
}
