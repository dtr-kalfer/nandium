<?php session_start(); 
require_once __DIR__ . '/config.php';
$locationName = defined('LOCATION_NAME') ? LOCATION_NAME : 'Please configure admin setup. Loading default: Burauen, Leyte';
$timezone = defined('TIMEZONE') ? TIMEZONE : 'Asia/Manila';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nandium Weather Dashboard</title>
	<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(to bottom, #261a02, #4c1c0e);
    color: white;
    margin: 0;
    padding: 0;
    text-align: center;
    background-color: #8c3c0f;
}

.header {
    padding: 30px 15px 10px;
}

h1 {
    margin: 0;
    font-size: 28px;
    color: #cfe6ff;
}

.subtitle {
    margin-top: 8px;
    color: #d8e9ff;
    font-size: 14px;
}

.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding: 30px 15px;
}

.card {
    background: rgba(255,255,255,0.08);
    border-radius: 14px;
    width: 260px;
    padding: 20px;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.15);
    transition: 0.25s;
}

.card:hover {
    transform: translateY(-3px);
    background: rgba(255,255,255,0.15);
}

.card h2 {
    margin-top: 10px;
    color: #ffffff;
}

.card p {
    font-size: 14px;
    color: #d0e4ff;
    min-height: 60px;
}

.button {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 16px;
    border-radius: 8px;
    background: #cc301e;
    color: #eee;
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s;
}

.button:hover {
    background: #ee502e;
}

.footer {
    margin-top: 30px;
    padding: 15px;
    font-size: 13px;
    color: #cfe6ff;
		border-top: 2px solid red;
		border-bottom: 2px solid red;
}

.badge {
    font-size: 40px;
}

.auth-links {
	color: #ccc;
	
}

a {
	color: #ddd;
	padding: 5px;
	text-decoration: none;
}

a:hover {
	color: #eee;
}

.mailto {
	padding: 5px;
	margin: 5px;
}

</style>
</head>
<body>
<div class="header">
    <h1>🌦 Nandium Weather Dashboard</h1>
    <div class="subtitle">
        24-Hour Forecast Comparison Dashboard<br>
        Open-source community weather intelligence platform for local government and agricultural planning.
				<br>
				<?php 
					echo "<p>Calibrated for: " . $locationName . "<br>";  
					echo "Timezone: " . $timezone . "</p>";  
				?>
    </div>
    <div class="auth-links">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff'): ?>
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! <a href="welcome_staff.php">Staff Dashboard</a> | <a href="logout.php">Logout</a>
        <?php else: ?>
            <a class="button" href="staff.php">Staff Login</a>
        <?php endif; ?>
         
    </div>
</div>

<div class="container">
  <div class="card">
      <div class="badge">🇳🇴</div>
      <h2>MET Norway</h2>
      <p>Forecast from the Norwegian Meteorological Institute. High-quality global weather modeling with cloud and precipitation data.
			<br>Source: https://api.met.no
			<br>Data licensed under: CC BY 4.0
			</p>
      <a class="button" href="./metweather/index.php" target="_blank">Open Forecast</a>
  </div>

  <div class="card">
      <div class="badge">🏴‍☠️</div>
      <h2>Pirate Weather</h2>
      <p>Dark Sky Forecast with detailed precipitation intensity and probability — excellent for rain planning.<br>
			Weather data provided by Pirate Weather<br>
			https://pirateweather.net<br>
			Apache License 2.0
			</p>
      <a class="button" href="./pirateweather/index.php" target="_blank">Open Forecast</a>
  </div>
</div>

<div class="footer">
    Built for community awareness and planning.
    <p><b>© 2026 Ferdinand Tumulak • Open Source under the MIT License</b></p>
		<div class="mailto"><a href="mailto:ferdinandtumulak@burauen.cc">ferdinandtumulak@burauen.cc</a></div>
		<br>
		<a class="button" href="privacy.html">Privacy</a>
		<br>
		<p>Disclaimer & Terms of Use<br>
This application is an open-source, experimental research tool that aggregates probabilistic data from third-party meteorological services (MET Norway and Pirate Weather). Forecasts are provided "as-is" without any explicit or implied warranties of accuracy or reliability.
Users should not rely solely on this application for high-risk outdoor activities, extreme sports, maritime travel, navigational planning, or critical life-safety decisions. Always refer to official government bulletins for weather warnings. By using this application, you agree that the developers and affiliated institutions assume no liability for any economic loss, property damage, or personal injury resulting from the use of this data.</p>
		
		<a class="button" href="feedback.php">Give Feedback</a>
</div>

</body>
</html>
