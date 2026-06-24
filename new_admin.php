<?php
require_once 'dbParams.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Clear existing users
    $conn->query("TRUNCATE TABLE users");

    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
    $stmt->bind_param("ss", $username, $password_hash);

    if ($stmt->execute()) {
        $message = '<div class="success">Admin account created successfully. For security, please delete this file (new_admin.php) now.</div>';
    } else {
        $message = '<div class="error">Error creating admin account: ' . $stmt->error . '</div>';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Initial Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Initial Admin Setup</h1>
        <p>Use this form to create the first administrator account. <strong>Warning:</strong> Submitting this form will delete all existing user accounts.</p>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label for="username">Admin Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Admin Password</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Create Admin Account">
        </form>
    </div>
</body>
</html>
