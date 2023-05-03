<?php
require('../db.php');
$sentId = $_GET['sentid'];

$objsql = "SELECT depart_id
    FROM edoc_sent
    WHERE sent_id = '$sentId' 
    ";
    
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_array($objrs);
    
$data[] = array(
        'departIdReceive'=>$objdata['depart_id'],
        'resp'=>$objsql
    );


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
