<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("content-type:text/javascript;charset=utf-8");

header("Content-Type: application/json; charset=utf-8", true, 200);

// $api_key = $_GET['api_key'];
// $secret_key = $_GET['secret_key'];

echo json_encode(
    array(
        "message" => "success",
        // "secret_key" => $secret_key,
        // "api_key" => $api_key
    ),JSON_UNESCAPED_UNICODE);

?>

