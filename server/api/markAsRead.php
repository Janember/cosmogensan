<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

include '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? 'single';
$notif_id = $data['id'] ?? null;
$user_id = $data['user_id'] ?? null;
$role = $data['role'] ?? null;

try {
    if ($action === 'single' && $notif_id) {
        $stmt = $pdo->prepare("
            INSERT INTO notification_reads (notification_id, user_id, is_read, read_at)
            VALUES (?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW()
        ");
        $stmt->execute([$notif_id, $user_id]);
    } elseif ($action === 'all') {
        // Fetch all relevant notifications first
        $stmt = $pdo->prepare("
            SELECT id FROM notifications
            WHERE role_target = :role
               OR role_target IS NULL
               OR user_target = :user_id
        ");
        $stmt->execute([
            ':role' => $role,
            ':user_id' => $user_id
        ]);
        $allNotifs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Insert/update read status
        $insertStmt = $pdo->prepare("
            INSERT INTO notification_reads (notification_id, user_id, is_read, read_at)
            VALUES (?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW()
        ");
        foreach ($allNotifs as $nid) {
            $insertStmt->execute([$nid, $user_id]);
        }
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
