<?php
// --- CORS setup ---
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../config/db_connect.php'; 

$user_id = $_POST['user_id'] ?? null;

if (!$user_id || !isset($_FILES['profile_picture'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$upload_dir = __DIR__ . '/../public/uploads/profile_pictures/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$file = $_FILES['profile_picture'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

$filename = 'user_' . intval($user_id) . '_' . time() . '.' . $ext;
$filepath = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = :filename WHERE id = :id");
        $stmt->execute([
            ':filename' => $filename,
            ':id'       => $user_id
        ]);

        echo json_encode([
            'success'  => true,
            'filename' => $filename,
            'url'      => '/uploads/profile_pictures/' . $filename
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Upload failed']);
}
