<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

$docNo = $request->documentNo;
$docUser = $request->documentUser;
$tempId = $request->documentTempid;
/* Location */
$location = '../../document';


$sql = "SELECT submittemp_pathfile FROM tbsubmit_temp
    WHERE submittemp_id='$tempId'
    AND submittemp_docNo = '$docNo'
    AND submittemp_user='$docUser'";
    
$res = mysqli_query($con, $sql);
$data = mysqli_fetch_array($res);
$path = $data[submittemp_pathfile];

$fileName = $location.$path;
$sql_del = "DELETE FROM tbsubmit_temp WHERE submittemp_id='$tempId' AND submittemp_docNo = '$docNo'";
if(mysqli_query($con, $sql_del)){
    unlink($fileName);
    $status = 1; 
}



$data[] = array(
    'status'=> $status,
    'path' => $path
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));