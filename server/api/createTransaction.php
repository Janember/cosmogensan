<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include('../config/db_connect.php');

$data = json_decode(file_get_contents('php://input'), true);

if (
    isset($data['reservation_id']) &&
    isset($data['user_id']) &&
    isset($data['downpayment']) &&
    isset($data['status'])
) {
    $reservation_id = $data['reservation_id'];
    $user_id = $data['user_id'];
    $amount_paid = $data['downpayment'];
    $status = $data['status'];

    try {
        $pdo->beginTransaction();

        $insertTransaction = "INSERT INTO transactions (reservation_id, user_id, amount_paid) 
                              VALUES (:reservation_id, :user_id, :amount_paid)";
        $stmt1 = $pdo->prepare($insertTransaction);
        $stmt1->execute([
            ':reservation_id' => $reservation_id,
            ':user_id' => $user_id,
            ':amount_paid' => $amount_paid,
        ]);

        // Update reservation status
        $updateStatus = "UPDATE reservations SET status = :status WHERE id = :reservation_id";
        $stmt2 = $pdo->prepare($updateStatus);
        $stmt2->execute([
            ':status' => $status,
            ':reservation_id' => $reservation_id
        ]);

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Payment recorded and reservation status updated.']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing one or more required fields.']);
}
