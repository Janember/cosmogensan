<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include '../config/db_connect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // adjust path if needed
$dotenv->load();

$data = json_decode(file_get_contents('php://input'), true);

try {
    $title        = trim($data['title'] ?? '');
    $message      = trim($data['message'] ?? '');
    $email        = $data['email'] ?? null;
    $type         = $data['type'] ?? null;
    $role_target  = $data['role_target'] ?? null;
    $user_target  = $data['user_target'] ?? null;
    $link         = $data['link'] ?? null;

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO notifications (title, message, type, role_target, user_target, link, created_at)
        VALUES (:title, :message, :type, :role_target, :user_target, :link, NOW())
    ");
    $stmt->execute([
        ':title'       => $title,
        ':message'     => $message,
        ':type'        => $type,
        ':role_target' => $role_target,
        ':user_target' => $user_target,
        ':link'        => $link
    ]);

    $pdo->commit();

    if (!empty($email)) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port       = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $title ?: 'New Notification';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2 style='color: #000;'>$title</h2>
                    <p>$message</p>
                    <hr>
                    <p style='font-size: 12px; color: #777;'>This is an automated message from Cosmopolitan Memorial Chapels.</p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Email sending failed — but don’t stop script
            error_log("Email failed to send: " . $mail->ErrorInfo);
        }
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
