<?php

include 'config.php';

$uploaddir = dirname(__DIR__).'/dist';
//$uploadfile = $uploaddir . DIRECTORY_SEPARATOR . basename($_FILES['uploadfile']['name']);
$uploadfile = $uploaddir . DIRECTORY_SEPARATOR . md5(time()) . '.mp3';

if (! isset($_FILES['uploadfile'])) {
    $response = array(
        'status' => 'error',
        'message' => 'The file could not be empty.'
    );
}
else if (! move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
    $response = array(
        'status' => 'error',
        'message' => 'The file could not uploaded to server.'
    );
}
else {
    $response = array(
        'status' => 'success',
        'data' => array(
            'file' => basename($uploadfile)
        )
    );
}

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);




