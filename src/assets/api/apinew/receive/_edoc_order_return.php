<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
@$edocId = $request->edocid;
@$sendId = $request->sendid;
@$departId_receive = $request->departid_receive; //หน่วงานรับ หน่วยงานที่ส่งหนังสือกลับ
@$departId_send = $request->departid_send; //หน่วยงานส่ง
@$userId = $request->userid; //ผู้ดำเนินการ
@$commentReturn = $request->comment_return;

@$sendtoDate = date('Y-m-d');
@$sendtoTime = date('H:i:s');

$objsql = "SELECT s.sent_no, s.pdf_name, s.pdf_path, d.depart_name 
    FROM edoc_sent s 
    LEFT JOIN department d ON s.depart_id_send = d.depart_id 
    WHERE s.sent_id = '$sendId'";

$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_array($objrs);

@$sentNo = $objdata['sent_no'];
@$pdfName = $objdata['pdf_name'];
@$pdfPath = $objdata['pdf_path'];
@$receiveDept = $objdata['depart_name'];

$status = 'false';

$receiveNo = $receiveNo + 1;
$sql = "UPDATE edoc_sent SET sent_status = 'R', sent_comment = '$commentReturn', return_status = 'Y'
    WHERE sent_id = '$sendId'";

if(mysqli_query($con, $sql)){
    $status = 'true';

    //add to table insert 
    $objsql = "INSERT INTO edoc_sent(
        sent_id,
        sent_no,
        sequence,
        date_sendto,
        time_sendto,
        edoc_id,
        user_id,
        depart_id_send,
        depart_id,
        receive_dept,
        pdf_name,
        pdf_path,
        sent_status,
        sent_comment,
        sequence_status,
        process_status,
        return_status,
        status_sent_doc
        )VALUES(
        null,
        '$sentNo',
        '',
        '$sendtoDate',
        '$sendtoTime',
        '$edocId',
        '$userId',
        '$departId_receive',
        '$departId_send',
        '$receiveDept',
        '$pdfName',
        '$pdfPath',
        '3',
        '$commentReturn',
        '2',
        '1',
        'Y',
        '0'
        )";

    mysqli_query($con, $objsql);

    ## edoc track and log ##
    $operation = "ส่งหนังสือกลับ";
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
        VALUES(null, '$edocId', '$sendtoTime', '$sendtoDate', '$operation', '$departId_receive', '$userId', '$ip_addr', '6')";
    mysqli_query($con, $objsql_track);
}

$data[] = array(
    'status'=>$status,
    'edocId'=>$edocId,
    'departid_receive'=>$departId_receive,
    'resp'=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>