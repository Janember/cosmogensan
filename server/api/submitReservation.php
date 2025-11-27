<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../config/db_connect.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = realpath(__DIR__ . '/../public/uploads/discount_id') . '/';
    $uploadedFile = '';

    if (!empty($_FILES['idUploaded']['name'])) {
        $originalName = basename($_FILES['idUploaded']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $uniqueName = uniqid('id_', true) . '.' . $extension;
        $targetFilePath = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['idUploaded']['tmp_name'], $targetFilePath)) {
            $uploadedFile = "/uploads/discount_id/" . $uniqueName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload ID.']);
            exit;
        }
    }

    $data = [
        'user_id' => $_POST['user_id'],
        'deceasedName' => $_POST['fullName'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate'],
        'casketID' => $_POST['casketID'],
        'chapelID' => $_POST['chapelID'],
        'color' => $_POST['color'],
        'additionalFeatures' => $_POST['additionalFeatures'],
        'instructions' => $_POST['instructions'],
        'city' => $_POST['city'],
        'barangay' => $_POST['barangay'],
        'street' => $_POST['street'],
        'houseNo' => $_POST['houseNo'],
        'userName' => $_POST['userName'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'idPath' => $uploadedFile,
        'price' => $_POST['price'],
        'rem_balance' => $_POST['price'],
        'size' => $_POST['size']
    ];

    try {
        $stmt = $pdo->prepare("INSERT INTO reservations (
            user_id, deceased_name, start_date, end_date, casket_id, chapel_id, color,
            additional_features, additional_instructions, city, barangay, street, house_no,
            user_name, email, phone, id_uploaded_path, price, rem_balance, size
        ) VALUES (
            :user_id, :deceasedName, :startDate, :endDate, :casketID, :chapelID, :color,
            :additionalFeatures, :instructions, :city, :barangay, :street, :houseNo,
            :userName, :email, :phone, :idPath, :price, :rem_balance, :size
        )");

        $stmt->execute($data);

        echo json_encode(['success' => true, 'message' => 'Reservation submitted successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
