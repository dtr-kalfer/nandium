<?php
session_start();

// Protect this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: staff.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome Staff</title>
    <link rel="stylesheet" href="styles.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <a href="logout.php" style="float: right;">Logout</a>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>This is the staff dashboard. You can add new locations for weather checks or view existing ones.</p>

        <h2>Staff Tools</h2>
        <ul>
            <li><a href="set_new_location.php">Add a New Location</a></li>
            <li><a href="check_location.php">Check Weather for a Location</a></li>
            <li><a href="remove_location.php">Remove an Existing Location</a></li>
        </ul>
    </div>
</body>
</html>