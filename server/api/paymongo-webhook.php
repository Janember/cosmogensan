<?php

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/paymongo_webhook_error.log');

$payload = file_get_contents("php://input");
$event = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}
$eventType = $event['data']['attributes']['type'] ?? '';

switch ($eventType) {
    case 'checkout_session.payment.paid':
        include('../config/db_connect.php');
        $paymentId = $event['data']['attributes']['data']['id'];
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'paid' WHERE id = ?");
        $stmt->execute([$paymentId]);
        
        $stmt = $pdo->prepare("SELECT user_id FROM transactions WHERE id = ?");
        $stmt->execute([$paymentId]);
        $user = $stmt->fetch();

        if ($user) {
            $userId = $user['user_id'];

            // 3. Update reservations for that user
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirming payment' WHERE user_id = ?");
            $stmt->execute([$userId]);

            error_log("✅ Payment successful. Transaction: $paymentId | User: $userId | Reservations updated.");
        } else {
            error_log("⚠️ Payment successful ($paymentId), but no user found.");
        }
        break;

    case 'payment.failed':
        error_log("❌ Payment failed: " . $event['data']['id']);
        break;

    case 'source.chargeable':
        error_log("💳 Source chargeable: " . $event['data']['id']);
        break;

    default:
        error_log("ℹ️ Unhandled event: " . $eventType);
}

http_response_code(200);
echo json_encode(["message" => "Webhook received"]);
