<?php
session_start();
require __DIR__ . '/../src/db_connect.php'; // Uses $conn from your db_connect.php

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Use prepared statements for security!
    $stmt = $conn->prepare("SELECT password FROM bot_admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header('Location: admin_panel.php');
            exit();
        }
    }
    $error = "Invalid username or password.";
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            min-height: 100vh;
            background: #e8f0fe;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 18px;
            box-shadow: 0 2px 24px 0 rgba(25,118,210,0.10), 0 1.5px 6px 0 rgba(25,118,210,0.11);
            width: 340px;
            max-width: 90vw;
            border-top: 5px solid #1976d2;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 1.7rem;
            color: #1976d2;
            letter-spacing: 1px;
            font-weight: 700;
            font-size: 2rem;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            padding: 13px 15px;
            border: 1.5px solid #e3eafc;
            border-radius: 7px;
            font-size: 1rem;
            background: #f8fafc;
            transition: border 0.2s, box-shadow 0.2s;
            color: #191f2b;
        }
        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            border: 1.5px solid #1976d2;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 2px #e3eafc;
        }
        .login-container button {
            padding: 13px;
            background: linear-gradient(90deg, #1976d2 80%, #63a4ff 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 1.08rem;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 0.7rem;
            transition: background 0.3s, box-shadow 0.2s;
            box-shadow: 0 2px 8px 0 rgba(25,118,210,0.05);
        }
        .login-container button:hover {
            background: linear-gradient(90deg, #1565c0 60%, #1976d2 100%);
            box-shadow: 0 4px 16px 0 rgba(25,118,210,0.08);
        }
        .error {
            background: #e3f2fd;
            color: #d32f2f;
            border: 1px solid #bbdefb;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 0.98rem;
        }
        @media (max-width: 400px) {
            .login-container { padding: 1.3rem 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>