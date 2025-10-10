<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
require_once('../config/db_connect.php');

header("Content-Type: application/json");

$chapel_id = $_GET['chapel_id'] ?? null;

if (!$chapel_id) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT start_date, end_date FROM reservations WHERE chapel_id = :chapel_id");
    $stmt->execute(['chapel_id' => $chapel_id]);
    $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($dates);
} catch (PDOException $e) {
    echo json_encode([]);
}
