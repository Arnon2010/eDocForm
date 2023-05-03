<?php
require('../db.php');
date_default_timezone_set("Asia/Bangkok");
$receiveId = $_GET['receive_id'];
$edocId = $_GET['edoc_id'];
$userId = $_GET['user_id'];//ผู้ดำเนินการ
$departId = $_GET['depart_id'];//รหัสหน่วยงานดำเนินการ

@$receiveDate = date('Y-m-d');
@$receiveTime = date('H:i:s');

$status = 'false';

$objsql = "UPDATE edoc_receive SET receive_status = '1'
    WHERE receive_id = '$receiveId'";
    
if($objrs = mysqli_query($con, $objsql)){
    $status = 'true';
    
    ## edoc track and log ##
    $operation = "สิ้นสุดการส่งต่อหนังสือ";
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
        VALUES(null, '$edocId', '$receiveTime', '$receiveDate', '$operation', '$departId', '$userId', '$ip_addr', '7')";
    mysqli_query($con, $objsql_track);
}

$data[] = array(
    'Status'=>$status,
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
