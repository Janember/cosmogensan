<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Max-Age: 86400"); // Cache for 1 day
    exit(0);
}

require_once('../config/db_connect.php');
require_once('../vendor/autoload.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$data = json_decode(file_get_contents("php://input"));
$email = $data->email ?? '';
$password = $data->password ?? '';
$hcaptcha_token = $data->hcaptcha_token ?? '';

$response = [];

if (empty($hcaptcha_token)) {
    echo json_encode(["status" => "error", "message" => "hCaptcha verification required"]);
    exit;
}

$secret = $_ENV['HCAPTCHA_SECRET'];
$verifyResponse = file_get_contents("https://hcaptcha.com/siteverify?secret=" . urlencode($secret) . "&response=" . urlencode($hcaptcha_token));
$captchaData = json_decode($verifyResponse, true);

if (!$captchaData['success']) {
    echo json_encode(["status" => "error", "message" => "hCaptcha verification failed"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = ["status" => "error", "message" => "Invalid email format"];
    echo json_encode($response);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, phone_number, address, password, profile_picture, hierarchy FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $response = [
            "status" => "success",
            "message" => "Login successful",
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "first_name" => $user['first_name'],
                "last_name" => $user['last_name'],
                "phone_number" => $user['phone_number'],
                "email" => $user['email'],
                "hierarchy" => $user['hierarchy'],
                "profile_picture" => $user['profile_picture']
            ]
        ];
    } else {
        $response = ["status" => "error", "message" => "Invalid email or password"];
    }
} catch (PDOException $e) {
    $response = ["status" => "error", "message" => "Database error"];
}

echo json_encode($response);
