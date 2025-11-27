<?php
// api/resetPassword.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit;
}

require_once '../config/db_connect.php'; // provides $pdo

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$token = trim($data['token'] ?? '');
$newPassword = trim($data['new_password'] ?? '');

if (!$token || !$newPassword) {
  echo json_encode(["success" => false, "message" => "Missing parameters"]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ? LIMIT 1");
  $stmt->execute([$token]);
  $user = $stmt->fetch();

  if (!$user) {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
    exit;
  }

  if (strtotime($user['reset_expires']) < time()) {
    echo json_encode(["success" => false, "message" => "Token expired"]);
    exit;
  }

  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

  $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
  $update->execute([$hashedPassword, $user['id']]);

  echo json_encode(["success" => true, "message" => "Password updated successfully"]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Database error"]);
}
