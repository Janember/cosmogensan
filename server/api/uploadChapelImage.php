<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

$uploadDir = __DIR__ . '/../public/chapels/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 5 * 1024 * 1024;

if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    exit;
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error.']);
    exit;
}

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
    exit;
}

if ($file['size'] > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File too large (max 5MB).']);
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('chapel_') . '.' . $ext;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Move uploaded file
$destination = $uploadDir . $filename;
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
    exit;
}

// Return success
echo json_encode([
    'success' => true,
    'filename' => $filename
]);
