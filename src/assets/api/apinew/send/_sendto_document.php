<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
$docDate = $request->docdate;
$docType = $request->doctype;
$userId = $request->userid;
$Secrets = $request->secrets;
$Rapid = $request->rapid;

$departIdUser = $request->depart_id_user;
$Comment = $request->comment;
$Headline = $request->headline;
$Receiver = $request->receiver;
$dateWrite = $request->datewrite;
$Sender = $request->sender;
$senderDepart = $request->senderdepart;
$destroyYear = $request->destroy_year;


$numberUniv = $request->number_univ;//ต้องการใช้เลขหนังสือของมหาลัย
$departId_agency = $request->depart_id_agency;//หน่วยงานส่วนราชการ ใช้เลขหนังสือของมหาลัย

if($numberUniv == true){
    $departId = $departId_agency;
    $departSentNo = 35;
}else{
    $departId = $departIdUser;
    $departSentNo = $departIdUser;
}

@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');
@$Year = $request->year_now;


$dateArray = explode("/",$docDate);
$docDateNew = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];

$yearDoc = $dateArray[2]+543;

//$sendNo = getdocNo_send($departId);

// $objsql = "SELECT sent_no FROM edoc_sent_no WHERE depart_id='$departIdUser' 
//     AND edoc_type_id = '$docType' 
//     AND sent_year = '$Year'";

$objsql = "SELECT sent_no FROM edoc_sent_no WHERE depart_id='$departSentNo' 
    AND edoc_type_id = '$docType' 
    AND sent_year = '$yearDoc'";

$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);

//กำหนดเลขหนังสือส่งถัดไป
if($objdata['sent_no'] == ''){
    $No = '1';
    $sql = "INSERT INTO edoc_sent_no (edoc_type_id, sent_no, sent_year, depart_id) 
                values('$docType', '$No', '$yearDoc', '$departSentNo')";
}else{
    $No = $objdata['sent_no'] + 1;
    $sql = "UPDATE edoc_sent_no SET sent_no='$No'
        WHERE depart_id='$departSentNo' 
        AND edoc_type_id = '$docType' 
        AND sent_year = '$yearDoc'";
}

//รหัสหนังสือของหน่วยงาน
$objsql2 = "SELECT depart_code FROM department WHERE depart_id='$departSentNo'";
$objrs2 = mysqli_query($con, $objsql2);
$objdata2 = mysqli_fetch_assoc($objrs2);
$departCode = $objdata2['depart_code'];

//$departCode = getdepartCode($departId);

$docNo = $departCode.''.$No;
$sendNo = $docNo;

$objsql3 = "INSERT INTO edoc(edoc_id,
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
    '$docType',
    '$docDateNew',
    '$sentDate',
    '$sentTime',
    '$Headline',
    '$Receiver',
    '$Comment',
    '$Secrets',
    '$Rapid',
    '$departId',
    '$senderDepart',
    '$userId',
    '$Sender',
    '$dateWrite',
    '$destroyYear',
    '0'
    )";
   
if(mysqli_query($con, $objsql3)){
    
    $edoc_id = mysqli_insert_id($con); // edoc id
    
    $status = "true";
    $objrs_sentNo = mysqli_query($con, $sql);//update and insert to table edoc_sent_no

    //inset table map edoc_univ_no กรณีมหาลัยออกเลขให้หน่วยงานอื่น
    if($numberUniv == true){
        $objsql_univNo = "INSERT INTO edoc_univ_no(edoc_id, depart_id) 
            VALUES ('$edoc_id','$departId')";
        mysqli_query($con, $objsql_univNo);
    }
    
    ## edoc track and log ##
    $operation = "ออกเลขหนังสือ";
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, status)
        VALUES(null, '$edoc_id', '$sentTime', '$sentDate', '$operation', '$departIdUser', '$userId', '1')";
    mysqli_query($con, $objsql_track);
    
}else{
    $status = "false";
}

$data[] = array(
    'status'=>$status,
    'departid'=>$departId,
    'datewrite'=>$dateWrite,
    'userid'=>$userId,
    'docno'=>$docNo,
    'sendno'=>$sendNo,
    'numberSend'=>$No,
    'destroyYear'=>$destroyYear,
    'edocId'=>$edoc_id
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>