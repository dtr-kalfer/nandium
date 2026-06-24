<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access Denied. You must be an administrator to manage credentials.');
}

require_once 'dbParams.php';
$message = '';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password_hash, $role);
    if ($stmt->execute()) {
        $message = '<div class="success">User added successfully.</div>';
    } else {
        $message = '<div class="error">Error adding user: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $message = '<div class="success">User deleted successfully.</div>';
    } else {
        $message = '<div class="error">Error deleting user: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all users
$users = $conn->query("SELECT id, username, role, created_at FROM users");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Credentials</title>
    <link rel="stylesheet" href="styles.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    th {
      color: #eee;
      background-color: #333;
    }
    </style>
		
</head>
<body>
    <div class="container">
        <a href="logout.php" style="float: right;">Logout</a>
        <h1>Manage User Credentials</h1>
        <?php echo $message; ?>
        <h2>Add New User</h2>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
            <input type="submit" name="add_user" value="Add User">
        </form>

        <h2>Existing Users</h2>
        <table>
            <tr><th>Username</th><th>Role</th><th>Created At</th><th>Action</th></tr>
            <?php while($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="delete_user" value="Remove" onclick="return confirm('Are you sure?');">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
