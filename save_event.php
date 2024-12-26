// save_event.php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Preflight request, respond successfully
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data) {
        if (isset($data['id'], $data['timestamp'], $data['localTime'], $data['message'], $data['source'])) {
            try {
                $stmt = $pdo->prepare("INSERT INTO events (event_id, timestamp, local_time, message, source) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['id'],
                    $data['timestamp'],
                    $data['localTime'],
                    $data['message'],
                    $data['source']
                ]);
                echo json_encode(['status' => 'success', 'message' => 'Event saved successfully']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
