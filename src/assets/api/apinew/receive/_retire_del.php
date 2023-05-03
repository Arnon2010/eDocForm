<?php
require('../db.php');
$retireId = $_GET['retire_id'];

$objsql = "DELETE FROM retire
    WHERE retire_id = '$retireId'";
    
if($objrs = mysqli_query($con, $objsql)){
    $status = 'true';
    //ลบข้อมูลผู้ได้รับมอบหมาย
    $objsql_delgr = "DELETE FROM delegate_retire WHERE retire_id = '$retireId'";
    mysqli_query($con, $objsql_delgr);
} else {
    $status = 'false';
    
}

$data[] = array(
    'Status'=>$status,
    'objsql'=>''
);



header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
