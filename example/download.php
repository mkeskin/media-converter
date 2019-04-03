<?php

/**
 * Load the config file for dist folder.
 */
include 'config.php';

// Output file path
$filepath = $config['dist_dir'] . DIRECTORY_SEPARATOR . $_GET['file'] . '.' . $_GET['format'];

$title = isset($_GET['title']) ? $_GET['title'] : basename($filepath);

if (file_exists($filepath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $title . '.' . $_GET['format'] . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));

    flush(); // Flush system output buffer

    readfile($filepath);

    exit;
}