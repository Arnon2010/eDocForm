<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

//@$location = '../../document';

@$departId_send = $_POST["departid_send"];
@$userId = $_POST["userid"];
@$edocId = $_POST["edocid"];
@$sentIdNext = $_POST["sentid_next"];
@$filePath = $_POST['file_path'];
@$fileName = $_POST["file_name"];

@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

@$docYear = date('Y');
     
    $objsqlUpdSeq = "UPDATE edoc_sent SET process_status = 'P', pdf_path = '$filePath', pdf_name='$fileName'
        WHERE sent_id = '$sentIdNext'";
    if(mysqli_query($con, $objsqlUpdSeq)){
        $status = 'true';
    }
   
    ## edoc track and log ##
    $operation = "ส่งต่อหนังสือ";
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
        VALUES(null, '$edocId', '$sendtoTime', '$sendtoDate', '$operation', '$departId_send', '$userId', '$ip_addr', '5')";
    mysqli_query($con, $objsql_track);
            
$data[] = array(
    'status'=>$status,
   
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
