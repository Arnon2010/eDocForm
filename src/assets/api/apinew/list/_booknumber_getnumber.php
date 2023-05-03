<?php
require('../db.php');
$facCode = $_GET[fac];
$tbookreceiveId = $_GET[tbookreceive_id];
$objsql = "SELECT MAX(nreceive_no) as No
    FROM tbnreceive
        WHERE fac_code = '$facCode' AND tbookreceive_id = '$tbookreceiveId'
    ";
    
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);
if($objdata['No'] != ''){
    $No = $objdata['No']+1;
}else{
    $No = '1';
}
$data[] = array(
        'nreceive'=>$No
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
