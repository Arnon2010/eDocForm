<?php
require('../db.php');
$mainId = $_GET[mainid];
//$User = $_GET[user];
$Order = $_GET[order];
$local = '../../document';
$sql = "SELECT * FROM tbsubmit_doc WHERE submitmain_id = '$mainId'";
$res = mysqli_query($con, $sql);
while($data = mysqli_fetch_array($res)){
    $path = $data[submitdoc_filepath];
    $filePath = $local.$path;
    unlink($filePath);
}

$objsql = "DELETE FROM tbsubmit_main WHERE submitmain_id = '$mainId' AND order_No = '$Order'";
    
if($objrs = mysqli_query($con, $objsql)){
    $sql_del = "DELETE FROM tbsubmit_doc WHERE submitmain_id = '$mainId'";
    mysqli_query($con, $sql_del);
    $status = '1';
}

$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
