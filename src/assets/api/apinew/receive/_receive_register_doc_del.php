<?php
require('../db.php');
$edocId = $_GET['edoc_id'];
$filePath = $_GET['file_path'];
@$Path = "../../document".$filePath;// $pathNew = '/edocsenttemp/DOC...';
$pathDoc = $Path;

$objsql_receive = "UPDATE edoc_receive SET pdf_path = NULL, pdf_name = NULL 
    WHERE edoc_id = '$edocId'";
mysqli_query($con, $objsql_receive);

$objsql_sent = "UPDATE edoc_sent SET pdf_path = NULL, pdf_name = NULL 
    WHERE edoc_id = '$edocId'";

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
