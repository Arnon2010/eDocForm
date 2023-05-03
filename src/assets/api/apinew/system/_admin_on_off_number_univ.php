<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$userId = $request->userId;
$statusAllow = $request->status_allow;

if($statusAllow == '1') {
    $allow = '0'; // เปลี่ยนเป็นไม่อนุญาต
} else {
    $allow = '1'; // เปลี่ยนเป็นอนุญาต
}

$objsql = "UPDATE edoc_user SET univ_number_allow = '$allow' WHERE user_id = '$userId'";
if(mysqli_query($con, $objsql)){
    $data[] = array(
        'status'=>'true'
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>