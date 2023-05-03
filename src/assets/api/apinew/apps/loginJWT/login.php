<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
//header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'database.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;
date_default_timezone_set('Asia/Bangkok');

$username = '';
$password = '';

$data = json_decode(file_get_contents("php://input"));
if ($data) {

    $username  = $data->username; 
    $password  = $data->password;
  
    
    //$hash      = password_hash($password, PASSWORD_DEFAULT); // generator ->  https://phppasswordhash.com/  

    $databaseService = new Database();
    $conn = $databaseService->getConnection();
    $sql = "SELECT * FROM ruts_users WHERE user_username ='$username' ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['user_id'];
        $firstname   = $row['user_firstname'];
        $lastname    = $row['user_lastname'];
        $password2   = $row['user_password'];

        if (password_verify($password, $password2)) {
            $secret_key = "ruts1234";
            $issuer_claim = "THE_ISSUER"; // this can be the servername
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); // issued at
           // $notbefore_claim = $issuedat_claim + 2; //not before in seconds
            $expire_claim = $issuedat_claim + 36000; // expire time in seconds
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
             //   "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "id" => $id,
                    "username" => $username,
                    
                ));

            http_response_code(200);

            $jwt = JWT::encode($token, $secret_key);
            echo json_encode(
                array(
                    "message" => "success",
                    "jwt" => $jwt,
                    "username" => $username,
                    "firstname" => $firstname ,
                    "lastname" => $lastname ,
                    "expireAt" => $expire_claim,
                ),JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "failed", "password" => $password));
        }
    }else{
        http_response_code(401);
        echo json_encode(array("message" => "File not found Username", "password" => $password));
    }

}
