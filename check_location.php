<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: staff.php');
    exit();
}

require_once 'dbParams.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch locations added by any staff member
$locations = $conn->query("SELECT id, location_name, latitude, longitude FROM staff_locations ORDER BY location_name");

// Check if weather is being viewed to dynamically expand the container
$is_viewing_weather = isset($_GET['location_id']) && (isset($_GET['view_met']) || $_GET['view_pirate']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Location Weather</title>
    <link rel="stylesheet" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
		/* Base container */
		.container {
				max-width: 700px;
				margin: 20px auto;
				background: #000; 
				padding: 25px;
				border-radius: 8px;
				box-shadow: 0 4px 8px rgba(255, 255, 255, 0.05);
				transition: max-width 0.3s ease; /* Smooth transition when resizing */
				box-sizing: border-box;
		}

		/* Reclaims the available screen space when displaying the 24 cards */
		.container.wide-view {
				max-width: 95%; /* Expands to desktop widths safely */
				width: 1400px;  /* Optimal maximum width for data-heavy grids */
		}

		/* Iframe Wrapper & Styling */
		.iframe-wrapper {
				width: 100%;
				margin-top: 20px;
				overflow: hidden;
		}

		#weather-frame {
				width: 100%;
				height: 70vh; /* Default sensible height before JS calculation */
				border: none;
				display: block;
				background: transparent;
		}

		/* UI Cleanup tweaks */
		.weather-form {
				margin-bottom: 20px;
		}
		.btn-group {
				margin-top: 15px;
		}
		.btn-group input {
				margin-right: 10px;
				margin-bottom: 10px;
				padding: 8px 15px;
				cursor: pointer;
		}

		/* Mobile Responsiveness fixes */
		@media (max-width: 768px) {
				.container {
						margin: 10px;
						padding: 15px;
				}
				.container.wide-view {
						width: 100%;
						max-width: 100%;
						margin: 0;
						border-radius: 0;
				}
				#weather-frame {
						/* Prevents scroll traps on touch devices if content expands */
						height: 80vh; 
				}
		}

		</style>
</head>
<body>
    <div class="container <?php echo $is_viewing_weather ? 'wide-view' : ''; ?>">
        <div class="nav-links">
            <a href="welcome_staff.php">Back to Staff Welcome</a> 
            <a href="logout.php" style="float: right;">Logout</a>
        </div>
        
        <h1>Check Weather for a Custom Location</h1>
        
        <form method="GET" action="" class="weather-form">
            <label for="location">Select a Location:</label>
            <select id="location" name="location_id">
                <?php while($row = $locations->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['location_id']) && $_GET['location_id'] == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(str_replace('_', ' ', $row['location_name'])); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="btn-group">
                <input type="submit" name="view_met" value="View with MET Norway">
                <input type="submit" name="view_pirate" value="View with Pirate Weather">
            </div>
        </form>

        <?php 
        if ($is_viewing_weather) {
            $location_id = intval($_GET['location_id']);
            $src = isset($_GET['view_met']) ? "./metweather/index.php" : "./pirateweather/index.php";
            
            echo "<div class='iframe-wrapper'>";
            echo "<iframe id='weather-frame' src='{$src}?staff_location_id={$location_id}' onload='resizeIframe(this)'></iframe>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
    function resizeIframe(obj) {
        try {
            // Re-adjusts frame height to match its internal content height
            obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
        } catch (e) {
            // Fallback if cross-origin or local caching prevents direct access
            obj.style.height = "75vh"; 
        }
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>