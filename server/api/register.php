<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:5173");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Max-Age: 86400"); // Cache for 1 day
    exit(0);
}
require_once('../config/db_connect.php');
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

require '../vendor/autoload.php';
$response = ['success' => false];

$mail = new PHPMailer(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $username = $input['username'];
    $first_name = $input['firstname'];
    $last_name = $input['lastname'];
    $email = trim($input['email'] ?? '');
    $phone_number = $input['phonenumber'];
    $password = $input['password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';
    $verification_code = bin2hex(random_bytes(32));
    $verification_link = "https://app.cosmogensan.com/verifyaccount?token=$verification_code";

    if (empty($email) || empty($password) || empty($confirm_password)) {
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    if ($password !== $confirm_password) {
        $response['message'] = 'Passwords do not match.';
        echo json_encode($response);
        exit;
    }

    if (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters.';
        echo json_encode($response);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $response['message'] = 'Email is already registered.';
            echo json_encode($response);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password, verification_code, hierarchy, phone_number, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, ?, NOW())");
        $stmt->execute([$username, $first_name, $last_name, $email, $hashedPassword, $verification_code, $phone_number]);

        $response['success'] = true;
        $response['message'] = 'Registration successful.';
    } catch (Exception $e) {
        $response['message'] = 'Server error: ' . $e->getMessage();
    }

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@cosmogensan.com';
        $mail->Password = '8nub!4w8ftK$TBdbzCsD';
        $mail->SMTPSecure = 'tls'; 
        $mail->Port = 587; 

        $mail->setFrom('no-reply@cosmogensan.com', 'Cosmopolitan Memorial Chapels');
        $mail->addAddress($email, $first_name);

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body = "Hello $first_name,<br><br>
            Please verify your email by clicking the link below:<br>
            <a href='$verification_link'>$verification_link</a><br><br>
            Thank you!";

        $mail->send();
        $response['success'] = true;
        $response['message'] = 'Registration successful! Check your email to verify your account.';
    } catch (Exception $e) {
        $response['message'] = 'Verification email could not be sent.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}
echo json_encode($response);