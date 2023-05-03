<?php
require('../db.php');
$receiveId = $_GET['receive_id'];
$edocId = $_GET['edoc_id'];
$departId = $_GET['depart_id'];//หน่วยงานรับหนังสือ
$filePath = $_GET['file_path'];
@$Path = "../../document".$filePath;// $pathNew = '/edocsenttemp/DOC...';
$pathDoc = $Path;

$objsql_receive = "UPDATE edoc_receive SET pdf_path = NULL, pdf_name = NULL 
    WHERE receive_id = '$receiveId'";
mysqli_query($con, $objsql_receive);

$objsql_sent = "UPDATE edoc_sent SET pdf_path = NULL, pdf_name = NULL 
    WHERE edoc_id = '$edocId' AND depart_id = '$departId'";

if(mysqli_query($con, $objsql_sent)){
    $status = 'true';
    @unlink($pathDoc);
}

$data[] = array(
    'status'=>$status  
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
