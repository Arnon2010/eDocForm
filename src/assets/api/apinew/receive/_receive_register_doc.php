<?php
require('../db.php');
require('../fn.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
/* Location */
@$location = '../../document/edoc/';

@$docNo = $_POST['docno'];
@$docDate = $_POST['docdate'];
@$docTypeId = $_POST['doctypeid'];
@$edocType = $_POST['edoctype'];
@$userId = $_POST['userid'];//user login send
@$departSender = $_POST['departsender'];// ผู้ส่ง
@$Sender = $_POST['sender'];// ผู้ส่ง
@$Secrets = $_POST['secrets'];
@$Rapid = $_POST['rapid'];
@$receiveDepartId = $_POST['receivedept']; //หน่วยงานรับ
@$sendDepartId = $_POST['senddept']; //จากหน่วยงาน
//@$sendDepartName = $_POST[senddept_name]; //ชื่อของหน่วยงาน
@$Comment = $_POST['comment'];
@$Headline = $_POST['headline'];
@$Receiver = $_POST['receiver'];
@$dateWrite = $_POST['datewrite'];
@$destroyYear = $_POST['destroy_year'];

@$newversionIs = $_POST['newversion_is'];

list($departNameReceive, $departParentReceive) = getDepartment($receiveDepartId); //ชื่อหน่วยงานรับ

list($departNameSend, $departParentSend) = getDepartment($sendDepartId); //ชื่อหน่วยงานส่ง


    
@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');

@$receiveDate = date('Y-m-d');
@$receiveTime = date('H:i:s');

@$Year = $_POST['year_now'];

/*
if($edocType == 'e'){
    $departSenderText = $departSender;//ชื่อของหน่วยงานภายนอก
}else{
    $departSenderText = $sendDepartName;
}
*/

if($sendDepartId == '1'){
    $departSenderText = $departSender;//ชื่อของหน่วยงานภายนอก
}else{
    $departSenderText = $departNameSend;
}

@$docYear = date('Y');

@$folder_depart = 'D00'.$sendDepartId.'/'.$docYear;
$structure = $location.''.$folder_depart.'/';
if (!file_exists($structure)) {
    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }
}
@$location_new = $structure;

$dateArray = explode("/",$docDate);
$docDateNew = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];

//$sendNo = getdocNo_send($departId);
$sendNo = $docNo;

$status = 'false';

$objsql = "INSERT INTO edoc(edoc_id,
    doc_no,
    edoc_type_id,
    doc_date,
    sent_date,
    sent_time,
    headline,
    receiver,
    comment,
    secrets,
    rapid,
    depart_id,
    sender_depart,
    user_id,
    sender,
    edoc_datewrite,
    destroy_year,
    status) VALUES(null,
    '$docNo',
    '$docTypeId',
    '$docDateNew',
    '$sentDate',
    '$sentTime',
    '$Headline',
    '$Receiver',
    '$Comment',
    '$Secrets',
    '$Rapid',
    '$sendDepartId',
    '$departSenderText',
    '$userId',
    '$Sender',
    '$dateWrite',
    '$destroyYear',
    '1'
    )";
   
if(mysqli_query($con, $objsql)){
    
    $edocId = mysqli_insert_id($con);
    
    $objsql_sent = "INSERT INTO edoc_sent(
                    sent_id,
                    sent_no,
                    date_sendto,
                    time_sendto,
                    edoc_id,
                    depart_id_send,
                    depart_id,
                    receive_dept,
                    pdf_name,
                    pdf_path,
                    sent_status)
                    VALUES(
                    null,
                    '$sendNo',
                    '$sentDate',
                    '$sentTime',
                    '$edocId',
                    '$sendDepartId',
                    '$receiveDepartId',
                    '$departNameReceive',
                    '',
                    '',
                    '2')";
                if(mysqli_query($con, $objsql_sent)){
                    // ออกเลขรับและข้อมูลการรับหนังสือ //
                    $objsql = "SELECT receive_no FROM edoc_receive_no 
                    WHERE depart_id='$receiveDepartId' 
                    AND receive_year = '$Year'";
                    $objrs = mysqli_query($con, $objsql);
                    $objdata = mysqli_fetch_assoc($objrs);
                    
                    /* Get number receive */
                    $No = $objdata['receive_no'];
                    
                    if($No == ''){
                        $receiveNo = '1';
                        $objsql_receiveNo = "INSERT INTO edoc_receive_no (depart_id, receive_no, receive_year)
                        values('$receiveDepartId', '$receiveNo', '$Year')";
                    }else{
                        $receiveNo = $No + 1;
                        $objsql_receiveNo = "UPDATE edoc_receive_no SET receive_no='$receiveNo', receive_year='$Year' WHERE depart_id='$receiveDepartId'";
                    }
                    mysqli_query($con, $objsql_receiveNo);
                            
                    @$receiveDate = date('Y-m-d');
                    @$receiveTime = date('H:i:s');
                    
                    $objsql_receive = "INSERT INTO edoc_receive(receive_id, edoc_type_id, receive_no, receive_date, receive_time, edoc_id, depart_id_send, depart_id, pdf_name, pdf_path, user_id, receive_type, status)
                        VALUES(null,
                        '$docTypeId',
                        '$receiveNo',
                        '$receiveDate',
                        '$receiveTime',
                        '$edocId',
                        '$sendDepartId',
                        '$receiveDepartId',
                        '',
                        '',
                        '$userId',
                        '2',
                        '1')";
                        
                    if(mysqli_query($con, $objsql_receive))
                        $status = "true";

                    
                    ## Signature Add to sign main table##
                    
                    $main_id = 'xx';

                    if($newversionIs == 'true'){
                        
                        $objsql_ma = "INSERT INTO sign_main SET 
                            edoc_id = '$edocId', 
                            depart_id = '$receiveDepartId',
                            user_id = '$userId',
                            create_date = '$receiveDate',
                            create_time = '$receiveTime',
                            doc_type = '2',
                            doc_receive_no = '$receiveNo',
                            main_type = '0'";
                        if(mysqli_query($con, $objsql_ma)){
                            // Get main id
                            $main_id = mysqli_insert_id($con);
                        }

                    }
                    
                    ## edoc track and log ##
                    $operation = "รับหนังสือจากภายนอก";
                    $ip_addr = $_SERVER['REMOTE_ADDR'];
                    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
                        VALUES(null, '$edocId', '$receiveTime', '$receiveDate', '$operation', '$receiveDepartId', '$userId', '$ip_addr', '5')";
                    mysqli_query($con, $objsql_track);
                }

}else{
    $status = "false";
}

$data[] = array(
    'status'=>$status,
    'departid'=>$sendDepartId,
    'datewrite'=>$dateWrite,
    'userid'=>$userId,
    'docno'=>$docNo,
    'sendno'=>$sendNo,
    'edocid'=>$edocId,
    'mainId'=>$main_id,
    'newversionIs'=>$newversionIs
);

// $data[] = array(
//     'status'=>$status,
//     'edocid'=>$edocId,
//     'respsql'=>$objsql_sent
// );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>