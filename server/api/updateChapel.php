<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include('../config/db_connect.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "Missing chapel ID."]);
    exit;
}

$id = intval($data['id']);
$name = $data['name'] ?? '';
$capacity = $data['capacity'] ?? 0;
$description = $data['description'] ?? '';
$status = $data['status'] ?? 'not available';
$image = $data['image'] ?? '';

try {
    $stmt = $pdo->prepare("
        UPDATE chapels 
        SET name = ?, capacity = ?, description = ?, status = ?, image = ?
        WHERE id = ?
    ");
    $stmt->execute([$name, $capacity, $description, $status, $image, $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Chapel updated successfully."]);
    } else {
        echo json_encode(["success" => true, "message" => "No changes made."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
