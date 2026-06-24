<?php
session_start();
require_once 'dbParams.php';

$message = '';
$last_submission_time = $_SESSION['last_submission_time'] ?? 0;
$time_since_last_submission = time() - $last_submission_time;
$cooldown_period = 300; // 5 minutes

if ($time_since_last_submission < $cooldown_period) {
    $wait_time = $cooldown_period - $time_since_last_submission;
    die("You must wait for {$wait_time} more seconds before submitting another feedback.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO feedback (used_for_outdoor_activity, activity_type, activity_other_details, activity_datetime, rainfall_accuracy, forecast_helpful, more_accurate_model, user_location, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssissss", $outdoor_activity, $activity_type, $other_details, $activity_datetime, $accuracy, $helpful, $model, $user_location, $comments);

    // Set parameters and execute
    $user_location = $_POST['user_location'];
    $activity_datetime = !empty($_POST['activity_datetime']) ? $_POST['activity_datetime'] : NULL;
    $comments = !empty($_POST['comments']) ? $_POST['comments'] : NULL;
    $outdoor_activity = $_POST['outdoor_activity'];
    $activity_type = $_POST['activity_type'];
    $other_details = ($activity_type === 'others') ? $_POST['activity_other_details'] : NULL;
    $accuracy = $_POST['rainfall_accuracy'];
    $helpful = $_POST['forecast_helpful'];
    $model = $_POST['more_accurate_model'];
    // $ip_address = $_SERVER['REMOTE_ADDR'];

    if ($stmt->execute()) {
        $_SESSION['last_submission_time'] = time();
        $message = '<div class="success">Thank you for your feedback!</div>';
    } else {
        $message = '<div class="error">Error: ' . $stmt->error . '</div>';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Feedback Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="styles.css">
		<style>
		.range-labels {
    display: flex;
    justify-content: space-between;
    width: 100%;
    font-size: 12px;
    margin-top: 5px;
		}
		
		textarea {
    width: 100%;
    box-sizing: border-box;
		}
		</style>
</head>
<body>
    <div class="container">
				<a href="index.php" style="float: right;">Home</a>
        <h1>Weather App Feedback</h1>
        <?php echo $message; ?>
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="user_location">Enter your location:</label>
                <input type="text" id="user_location" name="user_location" required>
            </div>
            <div class="form-group">
                <label>1. Did you use the app for any outdoor activity?</label>
                <input type="radio" name="outdoor_activity" value="yes" required> Yes
                <input type="radio" name="outdoor_activity" value="no"> No
            </div>
            <div class="form-group">
                <label>2. What specific activity did you use?</label>
                <input type="radio" name="activity_type" value="dry_palay" required> Dry Palay
                <input type="radio" name="activity_type" value="laundry"> Laundry
                <input type="radio" name="activity_type" value="outdoor_event"> Outdoor Event
                <input type="radio" name="activity_type" value="others"> Others, please specify:
                <input type="text" name="activity_other_details" id="other_details_text">
            </div>
            <div class="form-group">
                <label for="activity_datetime">When did the activity take place?:</label>
                <input type="datetime-local" id="activity_datetime" name="activity_datetime">
            </div>

						<div class="form-group">
								<label for="rainfall_accuracy">
										3. Rate the app's rainfall accuracy today (0 - Very Poor, 10 - Excellent):
								</label>

								<input
										type="range"
										id="rainfall_accuracy"
										name="rainfall_accuracy"
										min="0"
										max="10"
										step="1"
										required>

								<div class="range-labels">
										<span>0</span>
										<span>1</span>
										<span>2</span>
										<span>3</span>
										<span>4</span>
										<span>5</span>
										<span>6</span>
										<span>7</span>
										<span>8</span>
										<span>9</span>
										<span>10</span>
								</div>
						</div>

            <div class="form-group">
                <label>4. Did the app's forecast helped you?</label>
                <input type="radio" name="forecast_helpful" value="yes" required> Yes
                <input type="radio" name="forecast_helpful" value="no"> No
            </div>
            <div class="form-group">
                <label>5. Which weather forecast model do you think is more accurate?</label>
                <input type="radio" name="more_accurate_model" value="met_norway" required> MET Norway
                <input type="radio" name="more_accurate_model" value="pirate_weather"> Pirate Weather
                <input type="radio" name="more_accurate_model" value="none"> None
            </div>
            <div class="form-group">
                <label for="comments">Additional Comments:</label>
                <textarea id="comments" name="comments" rows="4" placeholder="Any other thoughts on the forecast or the app?"></textarea>
            </div>
            <input type="submit" value="Submit Feedback">
        </form>
    </div>
    <script>
        function validateForm() {
            const activityType = document.querySelector('input[name="activity_type"]:checked');
            const otherDetails = document.getElementById('other_details_text');
            if (activityType && activityType.value === 'others' && otherDetails.value.trim() === '') {
                alert('Please specify the other activity.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>