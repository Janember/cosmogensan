<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/db_connect.php'; // make sure this returns a PDO instance in $pdo

try {
    if (!isset($_GET['token'])) {
        echo json_encode(['success' => false, 'message' => 'Missing token']);
        exit;
    }

    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_code = ? AND is_verified = 0 LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
        $update->execute([$user['id']]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or already verified token']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
