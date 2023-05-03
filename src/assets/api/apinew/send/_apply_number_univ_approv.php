<?php
require('../db.php');

$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$mainId = $_GET['main_id'];
@$userId = $_GET['user_id'];

@$status = false;

$objsql_main = "UPDATE sign_main SET apply_number_univ = '0' 
    WHERE main_id = '$mainId'";
mysqli_query($con, $objsql_main);

$objsql_move = "UPDATE sign_move SET move_status = 'D' 
    WHERE main_id = '$mainId' 
    AND activity = 'ApplyNumber'";
if(mysqli_query($con, $objsql_move)){
        $status = true;
}

$data[] = array(
    'status'=>$status,
    'Activity'=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));


?>