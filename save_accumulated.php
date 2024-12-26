<?php
// save_accumulated.php - Save accumulated events to the database
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (is_array($data)) {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO events (event_id, timestamp, local_time, message, source) VALUES (?, ?, ?, ?, ?)");

        try {
            foreach ($data as $event) {
                if (isset($event['id'], $event['timestamp'], $event['localTime'], $event['message'], $event['source'])) {
                    $stmt->execute([
                        $event['id'],
                        $event['timestamp'],
                        $event['localTime'],
                        $event['message'],
                        $event['source']
                    ]);
                }
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    }
}
?>
