<?php
include __DIR__ . '/../src/db_connect.php';

$session_id = $_GET['session_id'] ?? '';
if (!$session_id) die(json_encode([]));

$stmt = $conn->prepare("SELECT * FROM bot_message WHERE session_id = ? ORDER BY created_at ASC");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'sender' => $row['sender'],
        'message' => $row['message'], // or 'text' if that's your column name
        'created_at' => $row['created_at']
    ];
}

header('Content-Type: application/json');
echo json_encode($messages);
?>