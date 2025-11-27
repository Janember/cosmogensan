<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once '../config/db_connect.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$email = trim($data['email'] ?? '');

if (!$email) {
  echo json_encode(["success" => false, "message" => "Email is required"]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT id, email, first_name FROM users WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if (!$user) {
    echo json_encode(["success" => false, "message" => "No account found with this email"]);
    exit;
  }

  $token = bin2hex(random_bytes(32));
  $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

  $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
  $update->execute([$token, $expires, $user['id']]);

  $frontendResetUrl = "https://app.cosmogensan.com/resetpassword?token=" . urlencode($token);

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'no-reply@cosmogensan.com';
    $mail->Password = '8nub!4w8ftK$TBdbzCsD';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('no-reply@cosmogensan.com', 'Cosmopolitan Memorial Chapels');
    $mail->addAddress($user['email'], $user['first_name'] ?? '');

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "
      <p>Hello " . htmlspecialchars($user['first_name'] ?? '') . ",</p>
      <p>We received a request to reset your password for your account.</p>
      <p>Click the link below to set a new password (valid for 1 hour):</p>
      <p><a href='$frontendResetUrl' target='_blank'>$frontendResetUrl</a></p>
      <p>If you didn’t request this, please ignore this email.</p>
      <br>
      <p>— Cosmopolitan Memorial Chapels</p>
    ";

    $mail->send();

    echo json_encode(["success" => true, "message" => "Password reset email sent successfully."]);
  } catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Failed to send email: {$mail->ErrorInfo}"]);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
