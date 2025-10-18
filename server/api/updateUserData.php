<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../config/db_connect.php'; 

$data = json_decode(file_get_contents('php://input'), true);

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Missing user ID']);
    exit;
}

$fields = ['first_name','last_name','email','phone_number','address'];
$updates = [];
$params = [];

foreach ($fields as $field) {
    if (isset($data[$field])) {
        $updates[] = "$field = :$field";
        $params[":$field"] = trim($data[$field]);
    }
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'No changes detected']);
    exit;
}

if (isset($data['email'])) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $stmt->execute([':email' => $data['email'], ':id' => $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        exit;
    }
}

if (isset($data['phone_number'])) {
    $phone = $data['phone_number'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone_number = :phone AND id != :id");
    $stmt->execute([':phone' => $phone, ':id' => $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Phone number already in use']);
        exit;
    }
}

$update_sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
$params[':id'] = $user_id;

try {
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute($params);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
