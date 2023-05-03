<?php
require('../db.php');
$edocId = $_GET['edocid'];
$departId = $_GET['departid'];

$objsql = "SELECT depart_id
    FROM edoc
    WHERE edoc_id = '$edocId'
    ";
    
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_array($objrs);
    
$data[] = array(
        'edocDepartId'=>$objdata['depart_id'],
        'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
