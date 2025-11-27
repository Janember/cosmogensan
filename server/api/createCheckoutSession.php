<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");
require __DIR__ . '/../vendor/autoload.php';
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$data = json_decode(file_get_contents('php://input'), true);

$reservation_id = $data['reservation_id'];
$user_id = $data['user_id'];
$rem_balance = $data['rem_balance'];
$payment_type = $data['payment_type'];
$name = $data['user_name'];

try {
    $stmt = $pdo->prepare("SELECT id, checkout_url, type FROM transactions WHERE reservation_id = ? AND type = ? LIMIT 1");
    $stmt->execute([$reservation_id, $payment_type]);
    $existingTransaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingTransaction && !empty($existingTransaction['checkout_url'])) {
        echo json_encode([
            'checkout_url' => $existingTransaction['checkout_url'],
            'transaction_id' => $existingTransaction['id']
        ]);
        exit;
    }

    $description = $payment_type === 'down'
    ? '50% Downpayment for chapel and casket reservation'
    : 'Remaining 50% Payment for full settlement of chapel and casket reservation';

    $client = new \GuzzleHttp\Client();
    
    $response = $client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
        'body' => json_encode([
            'data' => [
                'attributes' => [
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true,
                    'line_items' => [
                        [
                            'currency' => 'PHP',
                            'amount' => $rem_balance * 100,
                            'name' => 'Chapel and Casket Reservation',
                            'quantity' => 1
                        ]
                    ],
                    'payment_method_types' => ['gcash','card','grab_pay','paymaya'],
                    'description' => $description,
                    'metadata' => [
                        'reservation_id' => $reservation_id,
                        'user_id' => $user_id
                    ]
                ]
            ]
        ]),
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic c2tfdGVzdF9OSHVNY21rbjU1dmRyV052N0NWN0dKOWE6', // replace with your key
        ],
    ]);

    $body = json_decode($response->getBody(), true);

    $transactionId = $body['data']['id'] ?? null;
    $checkoutUrl = $body['data']['attributes']['checkout_url'] ?? null;

    if ($transactionId && $checkoutUrl) {
        if ($existingTransaction['type'] !== 'full'){
            $stmt = $pdo->prepare("INSERT INTO transactions (id, type, reservation_id, user_id, amount, status, checkout_url) 
                                VALUES (?, ?, ?, ?, ?, 'unpaid', ?)");
            $stmt->execute([
                $transactionId,
                $payment_type,
                $reservation_id,
                $user_id,
                $rem_balance,
                $checkoutUrl
            ]);
        }
        else {
            $stmt = $pdo->prepare("UPDATE transactions 
                                SET id = ?, checkout_url = ? 
                                WHERE reservation_id = ? AND type = ?");
            $stmt->execute([
                $transactionId,
                $checkoutUrl,
                $reservation_id,
                $payment_type
            ]);
        }
    }

    echo json_encode([
        'checkout_url' => $checkoutUrl,
        'transaction_id' => $transactionId
    ]);

} catch (\GuzzleHttp\Exception\RequestException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
