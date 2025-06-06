<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include('../config/db_connect.php'); // Include your database connection

// Read the raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Check if the required data is provided
if (isset($data['chapel_id']) && isset($data['status'])) {
    $chapel_id = $data['chapel_id'];
    $status = $data['status'];

    try {
        // Prepare the SQL query to update the reservation status
        $query = "UPDATE chapels SET status = :status WHERE id = :chapel_id";
        $stmt = $pdo->prepare($query);

        // Bind parameters to prevent SQL injection
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':chapel_id', $chapel_id, PDO::PARAM_INT);

        // Execute the query and check if the status was successfully updated
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update chapel status.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing chapel_id or status.']);
}
