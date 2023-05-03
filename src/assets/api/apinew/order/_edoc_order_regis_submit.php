<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
@$edocTypeId = $request->edoctype;
@$edocId = $request->edocid;
@$departId = $request->departid;
@$fileName = $request->filename;
@$filePath = $request->filepath;
@$userId = $request->userid;
@$receiveDate = date('Y-m-d');
@$receiveTime = date('H:i:s');
@$Year = date('Y')+543;

$objsql = "SELECT receive_no FROM edoc_receive_no WHERE depart_id='$departId'";
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);

/* Get number receive */
$receiveNo = $objdata['receive_no'];

if($receiveNo == ''){
    $receiveNo = '1';
    $sql = "INSERT INTO edoc_receive_no (depart_id, receive_no, receive_year) values('$departId', '$receiveNo', '$Year')";
}else{
    $receiveNo = $receiveNo + 1;
    $sql = "UPDATE edoc_receive_no SET receive_no='$receiveNo', receive_year='$Year' WHERE depart_id='$departId'";
}

$objsql = "SELECT depart_code FROM department WHERE depart_id='$departId'";
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);
$departCode = $objdata['depart_code'];
//$docNo = $departCode.''.$receiveNo;

$objsql = "INSERT INTO edoc_receive(receive_id, edoc_type_id, receive_no, receive_date, receive_time, edoc_id, depart_id, pdf_name, pdf_path, user_id, status)
    VALUES(null,
    '$edocTypeId',
    '$receiveNo',
    '$receiveDate',
    '$receiveTime',
    '$edocId',
    '$departId',
    '$fileName',
    '$filePath',
    '$userId',
    '1')";
    
if(mysqli_query($con, $objsql)){
    $status = 1;
    $objrs2 = mysqli_query($con, $sql);//update and insert to table edoc_receive_no
    ## Update status edoc_sent table ##
    ## 2 = confirm
    $objsql = "UPDATE edoc_sent SET sent_status = '2' WHERE edoc_id = '$edocId' AND depart_id = '$departId'";
    mysqli_query($con, $objsql);
    
}else{
    $status = 0;
}

$data[] = array(
    'status'=>$status,
    'edocid'=>$edocId,
    'departid'=>$departId
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>