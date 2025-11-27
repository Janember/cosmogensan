<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

include('../config/db_connect.php');

try {
    $query = "SELECT 
                    r.*, 
                    c.name AS chapel_name
                FROM 
                    reservations r
                LEFT JOIN 
                    chapels c
                ON 
                    r.chapel_id = c.id
                ORDER BY 
                    r.id DESC;
                ";
    $stmt = $pdo->query($query);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $reservations
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
