<?php

/**
 * Load the config file for dist folder.
 */
include 'config.php';


// File created by ffmpeg
$result = $config['dist_dir'] . DIRECTORY_SEPARATOR . $_POST['s'] . '.txt';

$content = file_exists($result) ? file_get_contents($result) : '';

// If result is empty then return error message.
if (! $result) {
    $response = array(
        'status' => 'error',
        'message' => 'The output file could not find.'
    );
    
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    die;
}

// Get duration of source
preg_match('/Duration: (.*?), start:/', $content, $matches);

$rawDuration = $matches[1];

// rawDuration is in 00:00:00.00 format. This converts it to seconds.
$ar = array_reverse(explode(':', $rawDuration));
$duration = floatval($ar[0]);
if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;

// Get the time in the file that is already encoded
preg_match_all('/time=(.*?) bitrate/', $content, $matches);

$rawTime = array_pop($matches);

// This is needed if there is more than one match
if (is_array($rawTime)){$rawTime = array_pop($rawTime);}

// rawTime is in 00:00:00.00 format. This converts it to seconds.
$ar = array_reverse(explode(':', $rawTime));
$time = floatval($ar[0]);
if (!empty($ar[1])) $time += intval($ar[1]) * 60;
if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;

// Calculate the progress
$progress = round(($time/$duration) * 100);

// Return the response message
$response = array(
    'status' => 'success',
    'data' => array(
        'duration' => $duration,
        'current_time' => $time,
        'progress' => $progress . '%'
    )
);

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);