<?php
require('../db.php');
$receiveId = $_GET['receive_id'];
$filePath = $_GET['file_path_retire'];
@$Path = "../../document".$filePath;// $pathNew = '/edocsenttemp/DOC...';
$path_doc_retired = $Path;

$objsql = "UPDATE edoc_receive SET pdf_path_retire = NULL, pdf_name_retire = NULL 
    WHERE receive_id = '$receiveId'";
if(mysqli_query($con, $objsql)){
    // delete file document retired
    $status = 'true';
    @unlink($path_doc_retired);
}

$data[] = array(
    'status'=>$status  
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
