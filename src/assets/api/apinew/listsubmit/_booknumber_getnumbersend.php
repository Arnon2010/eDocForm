<?php
require('../db.php');
$facCode = $_GET[fac];
$tbooksendId = $_GET[tbooksend_id];
$objsql = "SELECT MAX(nsend_no) as No
    FROM tbnsend
        WHERE fac_code = '$facCode' AND tbooksend_id = '$tbooksendId'
    ";
    
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);
if($objdata['No'] != ''){
    $No = $objdata['No']+1;
}else{
    $No = '1';
}
$data[] = array(
        'nsend'=>$No
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
