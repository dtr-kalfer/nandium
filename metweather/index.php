<?php
session_start(); // Needed for staff sessions
require_once __DIR__ . '/../dbParams.php'; // For staff location lookup

// Default settings (Public)
$is_staff_request = false;
$location_id = null;

// Check if this is a staff request
if (isset($_GET['staff_location_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    $is_staff_request = true;
    $location_id = (int)$_GET['staff_location_id'];
}

// --- Configuration Loading ---
if ($is_staff_request) {
    // Staff: Load details from the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $stmt = $conn->prepare("SELECT location_name, latitude, longitude, timezone FROM staff_locations WHERE id = ?");
    $stmt->bind_param("i", $location_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($loc = $result->fetch_assoc()) {
        $lat = $loc['latitude'];
        $lon = $loc['longitude'];
        $timezone = $loc['timezone'];
        $locationName = str_replace('_', ' ', $loc['location_name']);
        $cacheFile = __DIR__ . "/../staff_cache/MET_{$loc['location_name']}.json";
    } else {
        die('Staff location not found.');
    }
    $stmt->close();
    $conn->close();
    // Use global config for these
    require_once __DIR__ . '/../config.php';
    $cacheTTL = defined('CACHE_TTL') ? CACHE_TTL : 1800;
    $userAgent = defined('USER_AGENT') ? USER_AGENT : 'weatherapp@example.com';
} else {
    // Public: Load from config.php
    if (!file_exists(__DIR__ . '/../config.php')) {
        die('Configuration file not found. Please set up the application by running new_records.php.');
    }
    require_once __DIR__ . '/../config.php';
    $lat = defined('LATITUDE') ? LATITUDE : 10.9;
    $lon = defined('LONGITUDE') ? LONGITUDE : 124.8;
    $cacheTTL = defined('CACHE_TTL') ? CACHE_TTL : 1800;
    $userAgent = defined('USER_AGENT') ? USER_AGENT : 'weatherapp@example.com';
    $timezone = defined('TIMEZONE') ? TIMEZONE : 'Asia/Manila';
    $locationName = defined('LOCATION_NAME') ? LOCATION_NAME : 'Default Location';
    $cacheFile = __DIR__ . "/cache/met_cache.json";
}

date_default_timezone_set($timezone);

// --- API Fetch and Cache Logic ---
$url = "https://api.met.no/weatherapi/locationforecast/2.0/compact?lat={$lat}&lon={$lon}";
// The rest of the file remains the same, handling the API call, caching, and display...
// ... (The original display logic from metweather/index.php follows here)
?>

<?php

$forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] == '1'
    && isset($_GET['key']) && $_GET['key'] === '1234';

// (Diagnostic API check) bypass the 30min. delay and force a request --> index.php?refresh=1&key=1234

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: {$userAgent}\r\n"
    ]
];

$context = stream_context_create($options);

$fetchTime = date("Y-m-d H:i:s");
$usedCache = false;
$outage = "";

if (file_exists($cacheFile)) {
    $age = time() - filemtime($cacheFile);
    if ($age < $cacheTTL && !$forceRefresh) {
        $json = file_get_contents($cacheFile);
        $usedCache = true;
    } else {
        $json = @file_get_contents($url, false, $context);
        if ($json === false) {
            $outage = "⚠️ MET Weather Service unavailable";
            $json = file_get_contents($cacheFile);
            $usedCache = true;
        } else {
            file_put_contents($cacheFile, $json);
        }
    }
} else {
    $json = @file_get_contents($url, false, $context);
    if ($json !== false) {
        file_put_contents($cacheFile, $json);
    } else {
        die('Could not fetch weather data, and no cache is available.');
    }
}

$data = json_decode($json, true);
$timeseries = $data['properties']['timeseries'] ?? [];
$forecast = array_slice($timeseries, 0, 24);

$apiTimeUTC = $timeseries[0]['time'] ?? null;
$apiTimeLocal = "N/A";
if ($apiTimeUTC) {
    $dt = new DateTime($apiTimeUTC, new DateTimeZone("UTC"));
    $dt->setTimezone(new DateTimeZone($timezone));
    $apiTimeLocal = $dt->format("Y-m-d h:i A");
}

$cacheTime = file_exists($cacheFile) ? date("Y-m-d H:i:s", filemtime($cacheFile)) : $fetchTime;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $locationName; ?> 24 Hour Forecast</title>
  <link rel="stylesheet" type="text/css" href="weather3.css" />
	<style>
	.footer {
		text-align: center;
    margin-top: 30px;
    padding: 15px;
    font-size: 13px;
    color: #cfe6ff;
	}

        .button {
            display: inline-block;
            background-color: #e53e3e;
            color: var(--text-primary);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.2s ease;
            margin-top: 40px;
        }

        .button:hover {
            background-color: #c53030;
        }

	</style>
</head>
<body>

<h2><?php echo $locationName; ?> 24-Hour Forecast</h2>
<h3>
📍 Location: <?php echo $locationName; ?> <?php echo "[lat: " .  $lat . ", lon: " . $lon . "]"; ?><br>
🛰️ Weather Bureau: <a href="https://www.met.no/en">Norwegian Meteorological Institute</a><br> 
🕒 Forecast Time start: <?php echo $apiTimeLocal; ?><br>
📡 Data Retrieved: <?php echo $cacheTime; ?> (Server) <?php echo $usedCache ? "[CACHE] " : "[LIVE] "; echo $outage ? "<br>" . $outage : ""; ?>
</h3>

<div class="container">
<?php for ($i = 0; $i < count($forecast); $i++):
    $hour = $forecast[$i];
    $timeUTC = $hour['time'];
    $dt = new DateTime($timeUTC, new DateTimeZone("UTC"));
    $dt->setTimezone(new DateTimeZone($timezone));
    $displayTime = $dt->format("H:i");
    $details = $hour['data']['instant']['details'];
    $temp = $details['air_temperature'];
    $cloud = $details['cloud_area_fraction'] ?? 0;
    $symbol = $hour['data']['next_1_hours']['summary']['symbol_code'] ?? $hour['data']['next_6_hours']['summary']['symbol_code'] ?? "clearsky_day";
    $rainMM = $hour['data']['next_1_hours']['details']['precipitation_amount'] ?? $hour['data']['next_6_hours']['details']['precipitation_amount'] ?? 0;
    $iconPath = "icons/" . $symbol . ".svg";
    if (!file_exists($iconPath)) { $iconPath = "icons/clearsky_day.svg"; }
    $isRain = (stripos($symbol, 'rain') !== false);
    $isLight = (stripos($symbol, 'light') !== false);
    $isHeavy = (stripos($symbol, 'heavy') !== false);
?>
<div class="hour <?php echo $isRain ? 'rain ' : ''; echo $isLight ? 'light' : ''; echo $isHeavy ? 'heavy' : ''; ?> ">
    <div class="time"><?php echo $displayTime; ?></div>
    <img src="<?php echo $iconPath; ?>" alt="icon">
    <div class="temp"><?php echo $temp; ?>°C</div>
    <div class="cloud">☁ <?php echo $cloud; ?>%</div>
    <div class="cloud">☂︎ <?php echo $rainMM; ?> mm</div>
</div>
<?php endfor; ?>
</div>

<div class="footer">
    Nandium Weather Dashboard • Built for community awareness and planning.<br>
    © 2026 Ferdinand Tumulak
		<br>
		<a class="button" href="../privacy.html">Privacy</a>
		<br>
		<p>Disclaimer & Terms of Use<br>
This application is an open-source, experimental research tool that aggregates probabilistic data from third-party meteorological services (MET Norway and Pirate Weather). Forecasts are provided "as-is" without any explicit or implied warranties of accuracy or reliability.
Users should not rely solely on this application for high-risk outdoor activities, extreme sports, maritime travel, navigational planning, or critical life-safety decisions. Always refer to official government bulletins for weather warnings. By using this application, you agree that the developers and affiliated institutions assume no liability for any economic loss, property damage, or personal injury resulting from the use of this data.</p>
		
		<a class="button" href="../feedback.php">Give Feedback</a>
</div>

</body>
</html>
