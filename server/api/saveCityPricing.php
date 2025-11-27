<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");
include '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cities']) || !is_array($data['cities'])) {
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE delivery_rates SET price = :price WHERE id = :id");

    foreach ($data['cities'] as $city) {
        $stmt->execute([
            ':price' => $city['price'],
            ':id' => $city['id']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Prices updated successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
