<?php
include 'db_connect.php';

$sql = "SELECT s.session_id,
       MAX(m.created_at) as last_msg_time,
       (SELECT message FROM bot_message WHERE session_id = s.session_id ORDER BY created_at DESC LIMIT 1) as preview
    FROM bot_session s
    LEFT JOIN bot_message m ON s.session_id = m.session_id
    GROUP BY s.session_id
    ORDER BY last_msg_time DESC";

$result = $conn->query($sql);
$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}
echo json_encode($sessions);
?>