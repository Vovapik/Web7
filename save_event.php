<?php
// save_event.php

// Allow CORS (Cross-Origin Resource Sharing)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // Respond successfully to preflight requests
}

// Include the database connection
require_once 'db.php';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}
// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data) {
        // Validate required fields
        if (isset($data['id'], $data['timestamp'], $data['localTime'], $data['message'], $data['method'])) {
            try {
                // Prepare and execute the SQL query
                $stmt = $pdo->prepare("INSERT INTO events (event_id, timestamp, local_time, message, source) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['id'],
                    $data['timestamp'],
                    $data['localTime'],
                    $data['message'],
                    $data['method']
                ]);

                // Return success response
                echo json_encode(['status' => 'success', 'message' => 'Event saved successfully']);
            } catch (PDOException $e) {
                // Return error response in case of database failure
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            // Return error if required fields are missing
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        }
    } else {
        // Return error if the input data is invalid
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    }
} else {
    // Return error for unsupported HTTP methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
