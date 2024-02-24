<?php
function dd($args) {
    $args = func_get_args();
    foreach ($args as $arg) {
        var_dump($arg);
    }
    die();
}
function uploadFileWithToken($apiUrl, $bearerToken, $file) {
    $ch = curl_init();
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $serverUrl = $protocol . $host;

    $file_name = '/matlab_models/' . $file;

    // Prepare the file to be uploaded
    $postFields = array(
        'uploaded_model' => new CURLFile($file_name)
    );

    dd($file_name, $postFields);

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $bearerToken
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // Execute the POST request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }

    // Close the cURL session
    curl_close($ch);

    return $response;
}

$bearerToken = $_POST['access_token'];

// Example usage:
$apiUrl = 'https://api.iolab.sk/matlab';
$file = $_POST['file_name'];

try {
    $response = uploadFileWithToken($apiUrl, $bearerToken, $file);
    echo  json_encode((object)['response' => $response]);
} catch (Exception $e) {
    echo  json_encode((object)['error' => $e->getMessage()]);
}
