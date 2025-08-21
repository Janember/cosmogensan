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

if (!isset($data['item'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Item data missing']);
    exit;
}

$item = $data['item'];

$amount = intval($item['price'] * 100 * 0.5);
$name = $item['user_name'];

$client = new \GuzzleHttp\Client();

try {
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
                            'amount' => $amount,
                            'name' => 'Chapel and Casket Reservation',
                            'quantity' => 1
                        ]
                    ],
                    'payment_method_types' => ['gcash','card','grab_pay','paymaya'],
                    'description' => "50% Downpayment for $name",
                    'metadata' => [
                        'reservation_id' => $item['id'],
                        'user_id' => $item['user_id']
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

    if ($transactionId) {
        $stmt = $pdo->prepare("INSERT INTO transactions (id, reservation_id, user_id, amount, status) VALUES (?, ?, ?, ?, 'unpaid')");
        $stmt->execute([
            $transactionId,
            $item['id'],
            $item['user_id'],
            $amount*0.01
        ]);
    }

    echo json_encode([
        'checkout_url' => $checkoutUrl,
        'transaction_id' => $transactionId
    ]);

} catch (\GuzzleHttp\Exception\RequestException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}