<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['bot_session_id'])) {
    $_SESSION['bot_session_id'] = uniqid("session_");
    $sid = $_SESSION['bot_session_id'];
    $conn->query("INSERT IGNORE INTO bot_session (session_id) VALUES ('$sid')");
}
echo json_encode(["session_id" => $_SESSION['bot_session_id']]);
?>