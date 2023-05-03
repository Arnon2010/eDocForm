<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//include_once 'database.php';

require "vendor/autoload.php";
use \Firebase\JWT\JWT;
date_default_timezone_set('Asia/Bangkok');


$username = '';
$password = '';

$data = json_decode(file_get_contents("php://input"));

if ($data) {

    $username = strtolower($data->username);
    $password = $data->password;

    $loginUrl = 'http://elogin.rmutsv.ac.th';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'username=' . $username . '&password=' . $password);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $store = curl_exec($ch);
    //echo $store."<br>";
    if ($store != 'none') { //login ถูก
        // fname lname
        $f = explode(" ", $store);
        $firstname = $f[0];
        $lastname = $f[1];

        // JWT
        $secret_key = "ruts1234";
        $issuer_claim = "THE_ISSUER"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        //$notbefore_claim = $issuedat_claim + 2; //not before in seconds
        $expire_claim = $issuedat_claim + 36000; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "exp" => $expire_claim,
            "data" => array(
                "username" => $username,
            ));

        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        echo json_encode(
            array(
                "message" => "success",
                "jwt" => $jwt,
                "username" => $username,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "expireAt" => $expire_claim,
            ), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "failed", "password" => $password));
    }

    //$hash      = password_hash($password, PASSWORD_DEFAULT); // generator ->  https://phppasswordhash.com/
} 