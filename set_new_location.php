<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: staff.php');
    exit();
}

require_once 'dbParams.php';
$message = '';
$timezones = include 'timezones.php'; // Assuming you'll have a timezones array file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $location_name = str_replace(' ', '_', $_POST['location_name']);
    $lat = $_POST['latitude'];
    $lon = $_POST['longitude'];
    $timezone = $_POST['timezone'];
    $added_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO staff_locations (location_name, latitude, longitude, timezone, added_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddsi", $location_name, $lat, $lon, $timezone, $added_by);

    if ($stmt->execute()) {
        $message = '<div class="success">Location added successfully!</div>';
    } else {
        $message = '<div class="error">Error adding location: ' . $stmt->error . '</div>';
    }

    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Set New Location</title>
    <link rel="stylesheet" href="styles.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <a href="welcome_staff.php">Back to Staff Welcome</a> | <a href="logout.php" style="float: right;">Logout</a>
        <h1>Add a New Custom Location</h1>
        <p>This location will be saved for custom weather checks.</p>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label for="location_name">Location Name (e.g., San Fernando)</label>
            <input type="text" id="location_name" name="location_name" required>

            <label for="latitude">Latitude</label>
            <input type="text" id="latitude" name="latitude" required>

            <label for="longitude">Longitude</label>
            <input type="text" id="longitude" name="longitude" required>

            <label for="timezone">Timezone</label>
            <select id="timezone" name="timezone">
                <?php foreach ($timezones as $tz): ?>
                    <option value="<?php echo $tz; ?>" <?php echo ($tz === 'Asia/Manila' ? 'selected' : ''); ?>><?php echo $tz; ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Add Location">
        </form>
    </div>
</body>
</html>
