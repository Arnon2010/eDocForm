<?php
require('../db.php');
date_default_timezone_set("Asia/Bangkok");

@$mainId = $_GET['main_id'];
@$departId = $_GET['depart_id'];
@$edocId = $_GET['edoc_id'];
@$userId = $_GET['userid'];

@$timeUpdate = date('Y-m-d H:i:s');

$status = false;

// ปรับสถานะของ sign main
$objsqlUpdMain = "UPDATE sign_main SET main_status = '4' WHERE edoc_id = '$edocId'";
 mysqli_query($con, $objsqlUpdMain);

// ปรับสถานะของ sign move

// select detail sign_status = 2

$add_move = mysqli_query($con, "INSERT INTO  sign_move SET 
    main_id = '$mainId',
    activity = 'Done',
    user_id = '$userId',
    time = '$timeUpdate'");

if($add_move)
    $status = true;

$data[] = array(
    'status'=> $status,
    'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));