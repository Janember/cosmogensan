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
        include '../config/db_connect.php';
        $paymentId = $event['data']['attributes']['data']['id'];

        $stmt = $pdo->prepare("UPDATE transactions SET status = 'paid' WHERE id = ?");
        $stmt->execute([$paymentId]);

        $stmt = $pdo->prepare("SELECT user_id FROM transactions WHERE id = ?");
        $stmt->execute([$paymentId]);
        $user = $stmt->fetch();

        if ($user) {
            $userId = $user['user_id'];

            $stmt = $pdo->prepare("SELECT id, payment_type, rem_balance, user_id, status FROM reservations WHERE user_id = ?");
            $stmt->execute([$userId]);
            $reservations = $stmt->fetchAll();

            foreach ($reservations as $reservation) {
                $newPaymentType = match ($reservation['payment_type']) {
                    'down' => 'full',
                    'full' => 'paid',
                    default => $reservation['payment_type']
                };
                $newStatus = $reservation['status'] === 'approved' ? 'fully paid' : 'approved';
                $newBalance = $reservation['rem_balance']; 

                if ($reservation['payment_type'] === 'down') {
                    $newBalance = $reservation['rem_balance'] / 2;

                    $transactionId = null;

                    $payment_type = 'full';
                    $reservation_id = $reservation['id'];
                    $user_id = $reservation['user_id'];
                    $rem_balance = $newBalance;
                    $checkoutUrl = null; 
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (id, type, reservation_id, user_id, amount, status, checkout_url) 
                        VALUES (?, ?, ?, ?, ?, 'unpaid', ?)
                    ");
                    $stmt->execute([
                        $transactionId,
                        $payment_type,
                        $reservation_id,
                        $user_id,
                        $rem_balance,
                        $checkoutUrl
                    ]);

                } elseif ($reservation['payment_type'] === 'full') {
                    $newBalance = 0;
                } else {
                    $newBalance = $reservation['rem_balance'];
                }

                $updateStmt = $pdo->prepare("UPDATE reservations SET status = ?, payment_type = ?, rem_balance = ? WHERE id = ?");
                $updateStmt->execute([$newStatus, $newPaymentType, $newBalance, $reservation['id']]);
            }

            error_log("Payment successful. Transaction: $paymentId | User: $userId | Reservations updated.");
        } else {
            error_log("Payment successful ($paymentId), but no user found.");
        }
        break;

    case 'payment.failed':
        error_log("Payment failed: " . $event['data']['id']);
        break;

    case 'source.chargeable':
        error_log("Source chargeable: " . $event['data']['id']);
        break;

    default:
        error_log("ℹUnhandled event: " . $eventType);
}

http_response_code(200);
echo json_encode(["message" => "Webhook received"]);
