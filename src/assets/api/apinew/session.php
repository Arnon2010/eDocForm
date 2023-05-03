<?php
session_start();
$access_user = $_SESSION['UserName'];
$access_token = $_SESSION['token'];
$admin[] = array(
    'status' => 'true',
    'message' => 'this log in user',
    'username' => $access_user,
    'access_token' => $access_token
);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$admin));
?>