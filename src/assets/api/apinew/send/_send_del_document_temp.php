<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$tempId = $request->tempId;
@$edocId = $request->edocId;
@$filePath = $request->filePath;
@$fileName = $request->fileName;

/* Location */
@$location = '../../document';

@$Path = $location.$filePath;
$sql_del = "DELETE FROM edoc_sent_temp WHERE temp_id='$tempId'";
if(mysqli_query($con, $sql_del)){
    @unlink($Path);
    @$status = 1; 
}else{
    @$status = 0;
}

$data[] = array(
    'status'=> $status,
    'edocid'=> $edocId
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));