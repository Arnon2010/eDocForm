<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$edocId = $request->edocId;
@$sentId = $request->sentId;
@$filePath = $request->filePath;
@$fileName = $request->fileName;
/* Location */
@$location = '../../document';

//@$subPath = explode('_edoc_',$filePath);
//@$pathNew = $subPath[0].'_edoc_'.$fileName;
@$Path = "../../document".$filePath;// $pathNew = '/edoctemp/DOC...';
@$path_edoctemp = iconv("UTF-8","TIS-620",$Path);
$path_edoctemp = $Path;
$sql_del = "DELETE FROM edoc_sent WHERE sent_id='$sentId'";
if(mysqli_query($con, $sql_del)){
    @unlink($path_edoctemp);
    $status = 1; 
}else{
    $status = 0;
}

$data[] = array(
    'status'=> $status,
    'path' => $filePath,
    'edocid' => $edocId
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));