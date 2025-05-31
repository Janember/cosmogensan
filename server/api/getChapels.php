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
    $stmt = $pdo->prepare("SELECT id FROM caskets WHERE name = :name LIMIT 1");
    $stmt->execute(['name' => $name]);
    $casket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$casket) {
        echo json_encode(['success' => false, 'message' => 'Casket not found']);
        exit;
    }

    $casket_id = $casket['id'];

    $stmt = $pdo->prepare("
        SELECT chapels.name, chapels.image, chapels.status
        FROM chapels
        JOIN casket_chapels ON chapels.id = casket_chapels.chapel_id
        WHERE casket_chapels.casket_id = :casket_id
    ");
    $stmt->execute(['casket_id' => $casket_id]);
    $chapels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $chapelList = array_map(function ($chapel) {
        return [
            'name' => $chapel['name'],
            'image' => $chapel['image'],
            'capacity' => '50 People',  
            'features' => [
                'Fully air-conditioned chapel',
                'Water dispenser',
                '24/7 security and staff assistance',
            ],
            'status' => $chapel['status'],
        ];
    }, $chapels);

    // Return the formatted chapel data
    echo json_encode(['success' => true, 'data' => $chapelList]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
}
