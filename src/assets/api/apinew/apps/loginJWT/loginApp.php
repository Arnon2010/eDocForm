<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//include_once 'database.php';

require "vendor/autoload.php";
use \Firebase\JWT\JWT;
//date_default_timezone_set('Asia/Bangkok');

$username = '';
$password = '';

$data = json_decode(file_get_contents("php://input"));

if ($data) {

    $username = strtolower($data->epassport);
    $password = $data->pw;

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
        /*
        $f = explode(" ", $store);
        $firstname = $f[0];
        $lastname = $f[1];
         */
        include_once "../config.php";
        // //check 3D
        // // include_once '../connpdo_mssql.php';
        // // $conn = new pdo_mssql("172.16.162.39", "ACC3D", "ngruts", "angularapi");
        // // $sql = "SELECT STF_FNAME, STF_LNAME FROM vRUTS_epassport WHERE USERNAME_CISCO = '$username'";
        // // $user3D = $conn->return_sql($sql);

        // $userEdoc = mysqli_fetch_array(mysqli_query($mysqli,"SELECT tposition_name, e_passport 
        // FROM takeposition 
        // WHERE e_passport = '$username'"));

        $sql = "SELECT tposition_name, e_passport 
        FROM takeposition 
        WHERE e_passport = '$username'";
        $res = mysqli_query($mysqli,$sql);
        $userEdoc = mysqli_fetch_assoc($res);

        //$userEdoc = 'etete';
        if ($userEdoc) {
            //$firstname = $user3D[0]['STF_FNAME'];
            //$lastname = $user3D[0]['STF_LNAME'];

            $signerName = $userEdoc['tposition_name'];
            $e_passport = $userEdoc['e_passport'];
            
            // JWT
            $secret_key = "ruts-eSignature";
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
                    "username" => $e_passport,
                    "rmutsvmail" => $e_passport.'@rmutsv.ac.th',
                    "signerName" => $signerName,
                ));

            http_response_code(200);

            $jwt = JWT::encode($token, $secret_key);
            echo json_encode($jwt, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "failed", "status" => "ไม่พบสิทธิ์การใช้งาน"));
        }

    } else {
        http_response_code(401);
        echo json_encode(array("message" => "failed", "status" => "ชื่อผู้ใช้หรือรหัสไม่ถูกต้อง"));
    }

    //$hash      = password_hash($password, PASSWORD_DEFAULT); // generator ->  https://phppasswordhash.com/
}


