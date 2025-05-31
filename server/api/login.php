<?php
header("Access-Control-Allow-Origin: *");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Max-Age: 86400"); // Cache for 1 day
    exit(0);
}

require_once('../config/db_connect.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));
$email = $data->email ?? '';
$password = $data->password ?? '';

$response = [];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = ["status" => "error", "message" => "Invalid email format"];
    echo json_encode($response);
    exit;
}

try {
    // Query the database to find the user
    $stmt = $pdo->prepare("SELECT id, username, email, password, hierarchy FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $response = [
            "status" => "success",
            "message" => "Login successful",
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "email" => $user['email'],
                "hierarchy" => $user['hierarchy']
            ]
        ];
    } else {
        // Invalid credentials
        $response = ["status" => "error", "message" => "Invalid email or password"];
    }
} catch (PDOException $e) {
    // Handle any database error
    $response = ["status" => "error", "message" => "Database error"];
    // You can log $e->getMessage() in your server logs for debugging purposes
}

echo json_encode($response);
