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
    case 'payment.paid':
        include('../config/db_connect.php');
        $paymentId = $event['data']['id'];
        $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE payment_id = ?");
        $stmt->execute([$paymentId]);

        error_log("✅ Payment successful: " . $event['data']['id']);
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
