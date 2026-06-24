<?php
session_start();
require_once 'dbParams.php';

// 1. Admin-Only Access Control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch all feedback data
function getFeedbackData($db_connection) {
    $result = $db_connection->query("SELECT * FROM feedback ORDER BY submission_time DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// 2. Handle Download Requests
if (isset($_GET['download'])) {
    $feedback_data = getFeedbackData($conn);
    $timestamp = date('Y-m-d');

    if ($_GET['download'] === 'csv') {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=feedback_{$timestamp}.csv");

        $output = fopen('php://output', 'w');
        // Add header row
        fputcsv($output, array_keys($feedback_data[0]));
        // Add data rows
        foreach ($feedback_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    }

    if ($_GET['download'] === 'json') {
        header('Content-Type: application/json');
        header("Content-Disposition: attachment; filename=feedback_{$timestamp}.json");
        echo json_encode($feedback_data, JSON_PRETTY_PRINT);
        exit();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>List Feedback</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table { width: 100%; font-size: 0.9em; }
        th, td { padding: 8px; text-align: left; }
        .actions { margin-bottom: 20px; }
				.container {
						max-width: 1200px;
				}
    </style>
</head>
<body>
    <div class="container">
        <a href="new_records.php">Back to Admin Panel</a> | <a href="logout.php" style="float: right;">Logout</a>
        <h1>View Submitted Feedback</h1>

        <div class="actions">
            <form method="POST" action="">
                <input type="submit" name="generate_list" value="Generate List">
            </form>
        </div>

        <?php
        // 3. Generate List on Button Press
        if (isset($_POST['generate_list'])):
            $feedback_data = getFeedbackData($conn);

            if (empty($feedback_data)):
                echo "<p>No feedback has been submitted yet.</p>";
            else:
        ?>
        <div class="actions">
            <a href="?download=csv" class="button">Download as CSV</a>
            <a href="?download=json" class="button">Download as JSON</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Time</th>
                    <th>Used Outdoor?</th>
                    <th>Activity</th>
                    <th>Other Details</th>
                    <th>Activity Time</th>
                    <th>Accuracy (0-10)</th>
                    <th>Helpful?</th>
                    <th>Best Model</th>
                    <th>User Location</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedback_data as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['submission_time']; ?></td>
                    <td><?php echo htmlspecialchars($row['used_for_outdoor_activity']); ?></td>
                    <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['activity_other_details']); ?></td>
                    <td><?php echo htmlspecialchars($row['activity_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($row['rainfall_accuracy']); ?></td>
                    <td><?php echo htmlspecialchars($row['forecast_helpful']); ?></td>
                    <td><?php echo htmlspecialchars($row['more_accurate_model']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_location']); ?></td>
                    <td><?php echo htmlspecialchars($row['comments']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            endif; // end if empty
        endif; // end if generate_list
        ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>