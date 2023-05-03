<?php
require('../db.php');
$detailId = $_GET['detail_id'];
$filePath = $_GET['path'];

/* Location */
@$location = '../../document';

@$Path = $location.$filePath;
$sql_del = "DELETE FROM sign_detail WHERE detail_id='$detailId'";
if(mysqli_query($con, $sql_del)){
    @unlink($Path);
    @$status = 1; 
}else{
    @$status = 0;
}

$data[] = array(
    'status'=> $status,
    'resp'=>$Path
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));