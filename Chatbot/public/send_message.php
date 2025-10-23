<?php
include __DIR__ . '/../src/db_connect.php';
session_start();
$data = $_POST;
if (empty($data)) $data = json_decode(file_get_contents("php://input"), true);
$session_id = $data['session_id'] ?? ($_SESSION['bot_session_id'] ?? null);
$message = $data['message'] ?? null;
$sender = $data['sender'] ?? 'user';
if ($session_id && $message) {
    $stmt = $conn->prepare("INSERT INTO bot_message (session_id, message, sender) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $session_id, $message, $sender);
    $stmt->execute();
    $conn->query("INSERT IGNORE INTO bot_session (session_id) VALUES ('$session_id')");
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Missing params"]);
}
?>