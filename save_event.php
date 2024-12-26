// save_event.php - Save immediate event to the database
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the incoming POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data) {
        // Validate the required fields
        if (isset($data['id'], $data['timestamp'], $data['localTime'], $data['message'], $data['source'])) {
            // Prepare the database connection (assuming $pdo is your PDO instance)
            try {
                $stmt = $pdo->prepare("INSERT INTO events (event_id, timestamp, local_time, message, source) VALUES (?, ?, ?, ?, ?)");

                // Execute the statement with the data
                $stmt->execute([
                    $data['id'],
                    $data['timestamp'],
                    $data['localTime'],
                    $data['message'],
                    $data['source']
                ]);

                // Return success response
                echo json_encode(['status' => 'success', 'message' => 'Event saved successfully']);
            } catch (PDOException $e) {
                // Return error response in case of database failure
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            // Return error if any required field is missing
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        }
    } else {
        // Return error if the input data is invalid (unable to decode JSON)
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    // Handle requests with methods other than POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
