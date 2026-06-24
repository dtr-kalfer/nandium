<?php
session_start();
require_once 'dbParams.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify password AND role
        if (password_verify($password, $user['password_hash']) && $user['role'] === 'staff') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: welcome_staff.php");
            exit();
        } else {
            $message = '<div class="error">Invalid credentials or not a staff account.</div>';
        }
    } else {
        $message = '<div class="error">Invalid credentials.</div>';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Login</title>
    <link rel="stylesheet" href="styles.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1>Staff Login</h1>
        <p>This login is for authorized staff members only.</p>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <br>
        <a href="index.php">Return to Main Page</a>
    </div>
</body>
</html>
