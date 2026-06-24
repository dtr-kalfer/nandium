<?php
// Standalone API script for cron job - MET Norway

// Load main configuration
require_once __DIR__ . '/config.php';

// Use constants from config.php for the main location
$lat = defined('LATITUDE') ? LATITUDE : 10.9;
$lon = defined('LONGITUDE') ? LONGITUDE : 124.8;
$userAgent = defined('USER_AGENT') ? USER_AGENT : 'weatherapp_cron@example.com';

$url = "https://api.met.no/weatherapi/locationforecast/2.0/compact?lat={$lat}&lon={$lon}";

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: {$userAgent}\r\n"
    ]
];

$context = stream_context_create($options);
$json_data = @file_get_contents($url, false, $context);

if ($json_data === false) {
    // Log error and exit
    file_put_contents(__DIR__ . '/archive/cron_error.log', date('Y-m-d H:i:s') . " - MET API Fetch Failed\n", FILE_APPEND);
    exit(1);
}

// --- Save JSON File ---
$timestamp = date('Ymd_His');
$json_filename = "{$timestamp}-MET.json";
file_put_contents(__DIR__ . "/archive/{$json_filename}", $json_data);

// --- Convert and Save CSV File ---
$data = json_decode($json_data, true);
$timeseries = $data['properties']['timeseries'] ?? [];

if (empty($timeseries)) {
    exit(0); // No data to process
}

$csv_filename = "{$timestamp}-MET.csv";
$csv_handle = fopen(__DIR__ . "/archive/{$csv_filename}", 'w');

// Write CSV Header
fputcsv($csv_handle, [
    'time', 'air_temperature_celsius', 'cloud_area_fraction_percent', 'precipitation_amount_mm', 'symbol_code'
]);

// Write CSV Rows
foreach ($timeseries as $hour) {
    $details = $hour['data']['instant']['details'];
    $next_hour = $hour['data']['next_1_hours'] ?? $hour['data']['next_6_hours'];

    fputcsv($csv_handle, [
        $hour['time'],
        $details['air_temperature'] ?? null,
        $details['cloud_area_fraction'] ?? null,
        $next_hour['details']['precipitation_amount'] ?? 0,
        $next_hour['summary']['symbol_code'] ?? null
    ]);
}

fclose($csv_handle);

echo "MET Norway data archived successfully.";
?>