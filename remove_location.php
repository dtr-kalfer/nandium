<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: staff.php');
    exit();
}

require_once 'dbParams.php';
$message = '';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle location deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_location'])) {
    $location_id = $_POST['location_id'];
    $stmt = $conn->prepare("DELETE FROM staff_locations WHERE id = ?");
    $stmt->bind_param("i", $location_id);
    if ($stmt->execute()) {
        $message = '<div class="success">Location removed successfully.</div>';
    } else {
        $message = '<div class="error">Error removing location: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Fetch all locations
$locations = $conn->query("SELECT id, location_name FROM staff_locations ORDER BY location_name");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Remove Location</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="welcome_staff.php">Back to Staff Welcome</a> | <a href="logout.php" style="float: right;">Logout</a>
        <h1>Remove an Existing Location</h1>
        <?php echo $message; ?>
        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to remove this location? This action cannot be undone.');">
            <label for="location_id">Select a Location to Remove:</label>
            <select id="location_id" name="location_id">
                <?php while($row = $locations->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars(str_replace('_', ' ', $row['location_name'])); ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <input type="submit" name="remove_location" value="Remove Location">
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>