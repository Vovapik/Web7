// save_event.php - Save immediate event to the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'], $data['timestamp'], $data['localTime'], $data['message'], $data['source'])) {
        $stmt = $pdo->prepare("INSERT INTO events (event_id, timestamp, local_time, message, source) VALUES (?, ?, ?, ?, ?)");

        try {
            $stmt->execute([
                $data['id'],
                $data['timestamp'],
                $data['localTime'],
                $data['message'],
                $data['source']
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    }
}
