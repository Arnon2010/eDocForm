<?php
require('../db.php');
$edocId = $_GET['edocid'];
$departId = $_GET['departid'];
$receive_no = $_GET['receive_no'];

$objsql = "SELECT depart_id, depart_id_send
    FROM edoc_receive
    WHERE edoc_id = '$edocId' 
    AND  depart_id = '$departId' 
    AND receive_no = '$receive_no'
    ";
    
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_array($objrs);
    
$data[] = array(
        'departIdReceive'=>$objdata['depart_id'],
        'departIdSend'=>$objdata['depart_id_send'],
        'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
