<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use MediaConverter\Converter;

// Create a new conerter instance 
$converter = new Converter;
$converter->setDirectory(dirname(__DIR__).'/dist');

// Input file name
$name = basename($_POST['file'], '.'.strtolower(array_pop(explode('.', $_POST['file']))));
$ext = $_POST['format'];

// Make the error response message if error occurs at the end of the conversion.
if (! $converter->convert(dirname(__DIR__).'/dist/'.$_POST['file'], $name.'.'.$ext)) {
    $response = array(
        'status' => 'error',
        'message' => 'The file cound not converted.'
    );
}
else {
    $response = array(
        'status' => 'success',
        'data' => array(
            'output_file' => $name.$ext
        )
    );
}

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);