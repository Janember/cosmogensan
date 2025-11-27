<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

include '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$role = $data['role'] ?? null; 
$user_id = $data['user_id'] ?? null;

try {
    $query = "
        SELECT n.*, COALESCE(nr.is_read, 0) AS is_read
        FROM notifications n
        LEFT JOIN notification_reads nr
            ON n.id = nr.notification_id AND nr.user_id = ?
        WHERE n.user_target = ? OR n.role_target = ?
        ORDER BY n.created_at DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $user_id, $role]);

    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'notifications' => $notifications]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
