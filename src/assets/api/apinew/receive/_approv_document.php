<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

$docType = $request->doctype;//ประเภทหนังสือ
$userId = $request->userid;//ผู้ดำเนินการ
$Secrets = $request->secrets;//ความเร่งด่วน
$Rapid = $request->rapid;//ความลับ
$departIdUser = $request->depart_id_user;//หน่วยงานของ user
$Comment = $request->comment;//การปฏิบัติ
$Headline = $request->headline;//เรื่อง
$Receiver = $request->receiver;//เรียน
$dateWrite = $request->datewrite;//วันที่เสนอ
$Sender = $request->sender;//ชื่อผู้เสนอ
$senderDepart = $request->senderdepart;//หน่วยงานเสนอ
$destroyYear = $request->destroy_year;//อายุหนังสือ

$numberUniv = $request->number_univ;//ต้องการใช้เลขหนังสือของมหาลัย
$departId_agency = $request->depart_id_agency;//หน่วยงานส่วนราชการ ใช้เลขหนังสือของมหาลัย

if($numberUniv == true){
    $departId = $departId_agency;
}else{
    $departId = $departIdUser;
}

@$sentDate = date('Y-m-d');
@$sentTime = date('H:i:s');
@$Year = $request->year_now;

$dateArray = explode("/",$docDate);
$docDateNew = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];

// Insert to table "edoc"
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
   
if(mysqli_query($con, $objsql)){
    
    $edoc_id = mysqli_insert_id($con); // Get "edoc id" last insert

    $objsql2 = "INSERT INTO sign_main SET 
        edoc_id = '$edoc_id', 
        depart_id = '$departId',
        user_id = '$userId',
        create_date = '$sentDate',
        create_time = '$sentTime',
        doc_type = '1'";
    if(mysqli_query($con, $objsql2))
        $status = "true";

    $mainId = mysqli_insert_id($con);
        
    ## edoc track and log ##
    /*
    $operation = "เสนอหนังสือ";
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, status)
        VALUES(null, '$edoc_id', '$sentTime', '$sentDate', '$operation', '$departIdUser', '$userId', 'A')";
    mysqli_query($con, $objsql_track);
    */
    
}else{
    $status = "false";
}

$data[] = array(
    'status'=>$status,
    'edocId'=>$edoc_id,
    'mainId'=>$mainId
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>