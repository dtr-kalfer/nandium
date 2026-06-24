<?php
session_start();
require_once __DIR__ . '/../dbParams.php';

$is_staff_request = false;
$location_id = null;

if (isset($_GET['staff_location_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    $is_staff_request = true;
    $location_id = (int)$_GET['staff_location_id'];
}

if ($is_staff_request) {
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
        $cacheFile = __DIR__ . "/../staff_cache/PIR_{$loc['location_name']}.json";
    } else {
        die('Staff location not found.');
    }
    $stmt->close();
    $conn->close();
    require_once __DIR__ . '/../config.php';
    $cacheTTL = defined('CACHE_TTL') ? CACHE_TTL : 1800;
    $apiKey = defined('PIRATE_API_KEY') ? PIRATE_API_KEY : '';
} else {
    if (!file_exists(__DIR__ . '/../config.php')) {
        die('Configuration file not found.');
    }
    require_once __DIR__ . '/../config.php';
    $lat = defined('LATITUDE') ? LATITUDE : 10.98;
    $lon = defined('LONGITUDE') ? LONGITUDE : 124.9;
    $cacheTTL = defined('CACHE_TTL') ? CACHE_TTL : 1800;
    $apiKey = defined('PIRATE_API_KEY') ? PIRATE_API_KEY : '';
    $timezone = defined('TIMEZONE') ? TIMEZONE : 'Asia/Manila';
    $locationName = defined('LOCATION_NAME') ? LOCATION_NAME : 'Default Location';
    $cacheFile = __DIR__ . "/cache/pirate_cache.json";
}

if (empty($apiKey)) {
    die('Pirate Weather API Key is not configured.');
}

date_default_timezone_set($timezone);

$url = "https://api.pirateweather.net/forecast/{$apiKey}/{$lat},{$lon}?units=si&exclude=minutely,daily,alerts";

$forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] == '1' && isset($_GET['key']) && $_GET['key'] === '1234';
// (Diagnostic API check) bypass the 30min. delay and force a request --> index.php?refresh=1&key=1234

$fetchTime = date("Y-m-d H:i:s");
$usedCache = false;
$outage = "";

if (file_exists($cacheFile)) {
    $age = time() - filemtime($cacheFile);
    if ($age < $cacheTTL && !$forceRefresh) {
        $json = file_get_contents($cacheFile);
        $usedCache = true;
    } else {
        $json = @file_get_contents($url);
        if ($json === false) {
            $outage = "⚠️ Pirate Weather Service unavailable";
            $json = file_get_contents($cacheFile);
            $usedCache = true;
        } else {
            file_put_contents($cacheFile, $json);
        }
    }
} else {
    $json = @file_get_contents($url);
    if ($json !== false) {
        file_put_contents($cacheFile, $json);
    } else {
        die('Could not fetch weather data, and no cache is available.');
    }
}

$data = json_decode($json, true);
$hourly = $data['hourly']['data'] ?? [];
$forecast = array_slice($hourly, 0, 24);

$apiTimeUTC = $forecast[0]['time'] ?? null;
$apiTimeLocal = "N/A";
if ($apiTimeUTC) {
    $dt = new DateTime("@$apiTimeUTC");
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
  <link rel="stylesheet" href="weather3.css">
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
    margin-top: 12px;
    padding: 10px 16px;
    border-radius: 8px;
    background: #1e90ff;
    color: white;
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s;
	}
	</style>
</head>
<body>

<h2><?php echo $locationName; ?> 24-Hour Forecast</h2>
<h3>
📍 Location: <?php echo $locationName; ?> <?php echo "[lat: " .  $lat . ", lon: " . $lon . "]"; ?><br>
🛰️ Weather Bureau: Pirate Weather<br>
🕒 Forecast Time start: <?php echo $apiTimeLocal; ?><br>
📡 Data Retrieved: <?php echo $cacheTime; ?> <?php echo $usedCache ? "[CACHE]" : "[LIVE]"; echo $outage ? "<br>" . $outage : ""; ?>
</h3>

<div class="container">
<?php foreach ($forecast as $hour): 
    $timestamp = $hour['time'];
    $dt = new DateTime("@$timestamp");
    $dt->setTimezone(new DateTimeZone($timezone));
    $displayTime = $dt->format("H:i");
    $temp  = $hour['temperature'] ?? 0;
    $cloud = round(($hour['cloudCover'] ?? 0) * 100);
    $rainMM = $hour['precipIntensity'] ?? 0;
    $rainProb = round(($hour['precipProbability'] ?? 0) * 100);
    $summary = $hour['summary'] ?? "Clear";
		
		$append_class = "";

		if ($rainMM >= 2.0)      $append_class = "rain20";
		elseif ($rainMM > 1.8)   $append_class = "rain18";
		elseif ($rainMM > 1.6)   $append_class = "rain16";
		elseif ($rainMM > 1.4)   $append_class = "rain14";
		elseif ($rainMM > 1.2)   $append_class = "rain12";
		elseif ($rainMM > 1.0)   $append_class = "rain10";
		elseif ($rainMM > 0.8)   $append_class = "rain08";
		elseif ($rainMM > 0.6)   $append_class = "rain06";
		elseif ($rainMM > 0.4)   $append_class = "rain04";
		elseif ($rainMM > 0.2)   $append_class = "rain02";
		elseif ($rainMM > 0.0)   $append_class = "rain00";		
?>
<div class="hour <?php echo $append_class; ?>">
    <div class="time"><?php echo $displayTime; ?></div>
    <div class="symbol"><?php echo htmlspecialchars($summary); ?></div>
    <div class="temp"><?php echo $temp; ?>°C</div>
    <div class="cloud">☁ <?php echo $cloud; ?>%</div>
    <div class="cloud">☂ <?php echo $rainMM; ?> mm</div>
    <div class="cloud">🌧 <?php echo $rainProb; ?>%</div>
</div>
<?php endforeach; ?>
<div class="footer">
    Nandium Weather Dashboard • Built for community awareness and planning<br>
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
