<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
@$edocId = $request->edocid;
@$sendId = $request->sendid;
@$departId_receive = $request->departid_receive; //หน่วงานรับ หน่วยงานที่ส่งหนังสือกลับ
@$userId = $request->userid; //คนดำเนิดการ


@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

$status = 'false';

$receiveNo = $receiveNo + 1;
$sql = "UPDATE edoc_sent SET sent_status = '5'
    WHERE sent_id = '$sendId'";

if(mysqli_query($con, $sql)){
    $status = 'true';
    ## edoc track and log ##
    $operation = "รับทราบหนังสือ";
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
        VALUES(null, '$edocId', '$sendtoTime', '$sendtoDate', '$operation', '$departId_receive', '$userId', '$ip_addr', '8')";
    mysqli_query($con, $objsql_track);
}

$data[] = array(
    'status'=>$status,
    'resp'=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>