<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$departIdUser = $request->depart_id;
$docDate = $request->edoc_date;
$docType = $request->doctype;
$rapidId = $request->rapidid;
$secretsId = $request->secretsid;
$Comment = $request->comment;
$Headline = $request->headline;
$Receiver = $request->receiver;
$edocId = $request->edocid;
$destroyYear = $request->destroy_year;
$sentDate = $request->send_date;
$sentTime = $request->send_time;

$dateArray = explode("/",$docDate);
@$docDateNew = ($dateArray[2]).'-'.$dateArray[1].'-'.$dateArray[0];

$sentDateStr = explode("/",$sentDate);
@$sentDateNew = ($sentDateStr[2]).'-'.$sentDateStr[1].'-'.$sentDateStr[0];

$numberUniv = $request->number_univ;//ต้องการใช้เลขหนังสือของมหาลัย
$departId_agency = $request->depart_id_agency;//หน่วยงานส่วนราชการ ใช้เลขหนังสือของมหาลัย

if($numberUniv == true){//ออกให้เลขให้หน่วยงานอื่น
    $departId = $departId_agency;
    //inset table map edoc_univ_no กรณีมหาลัยออกเลขให้หน่วยงานอื่น
    $objsql_univNo = "INSERT INTO edoc_univ_no(edoc_id, depart_id) 
        VALUES ('$edocId','$departId')";
    mysqli_query($con, $objsql_univNo);

}else{
    $departId = $departIdUser;
}

$objsql = "UPDATE edoc SET
    edoc_type_id = '$docType',
    depart_id = '$departId',
    secrets = '$secretsId',
    rapid = '$rapidId',
    doc_date = '$docDateNew',
    sent_date = '$sentDateNew',
    sent_time = '$sentTime',
    headline = '$Headline',
    receiver = '$Receiver',
    comment = '$Comment',
    destroy_year = '$destroyYear'
    WHERE edoc_id = '$edocId'";
   
if(mysqli_query($con, $objsql)){
    $status = "true";
}else{
    $status = "false";
}

$data[] = array(
    'status'=>$status,
    'resp'=>$objsql
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>