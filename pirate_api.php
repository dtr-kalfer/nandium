<?php
// Standalone API script for cron job - Pirate Weather

require_once __DIR__ . '/config.php';

$lat = defined('LATITUDE') ? LATITUDE : 10.98;
$lon = defined('LONGITUDE') ? LONGITUDE : 124.9;
$apiKey = defined('PIRATE_API_KEY') ? PIRATE_API_KEY : '';

if (empty($apiKey)) {
    file_put_contents(__DIR__ . '/archive/cron_error.log', date('Y-m-d H:i:s') . " - Pirate Weather API Key Missing\n", FILE_APPEND);
    exit(1);
}

$url = "https://api.pirateweather.net/forecast/{$apiKey}/{$lat},{$lon}?units=si&exclude=minutely,daily,alerts";

$json_data = @file_get_contents($url);

if ($json_data === false) {
    file_put_contents(__DIR__ . '/archive/cron_error.log', date('Y-m-d H:i:s') . " - Pirate Weather API Fetch Failed\n", FILE_APPEND);
    exit(1);
}

// --- Save JSON File ---
$timestamp = date('Ymd_His');
$json_filename = "{$timestamp}-PIR.json";
file_put_contents(__DIR__ . "/archive/{$json_filename}", $json_data);

// --- Convert and Save CSV File ---
$data = json_decode($json_data, true);
$hourly_data = $data['hourly']['data'] ?? [];

if (empty($hourly_data)) {
    exit(0);
}

$csv_filename = "{$timestamp}-PIR.csv";
$csv_handle = fopen(__DIR__ . "/archive/{$csv_filename}", 'w');

fputcsv($csv_handle, [
    'time', 'temperature_celsius', 'cloud_cover_percent', 'precipitation_intensity_mm_hr', 'precipitation_probability_percent', 'summary'
]);

foreach ($hourly_data as $hour) {
    fputcsv($csv_handle, [
        date('Y-m-d H:i:s', $hour['time']),
        $hour['temperature'] ?? null,
        round(($hour['cloudCover'] ?? 0) * 100),
        $hour['precipIntensity'] ?? 0,
        round(($hour['precipProbability'] ?? 0) * 100),
        $hour['summary'] ?? null
    ]);
}

fclose($csv_handle);

echo "Pirate Weather data archived successfully.";
?>