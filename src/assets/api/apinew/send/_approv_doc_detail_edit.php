<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

$departId = $request->depart_id;
$docType = $request->doctype;
$rapidId = $request->rapidid;
$secretsId = $request->secretsid;
$Comment = $request->comment;
$Headline = $request->headline;
$Receiver = $request->receiver;
$edocId = $request->edocid;
$destroyYear = $request->destroy_year;


$objsql = "UPDATE edoc SET
    edoc_type_id = '$docType',
    secrets = '$secretsId',
    rapid = '$rapidId',
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