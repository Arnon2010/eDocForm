<?php
require('../db.php');
$edocId = $_GET['edoc_id'];
$departId = $_GET['depart_id'];
$departIdSend = $_GET['depart_id_send'];

$objsql = "SELECT depart_id
    FROM edoc_receive
    WHERE edoc_id = '$edocId'  
    AND depart_id = '$departId' 
    AND depart_id_send = '$departIdSend'
    ";

// $objsql = "SELECT depart_id
//     FROM edoc_receive
//     WHERE edoc_id = '$edocId'  
//     AND depart_id = '$departId' 
//     ";
    
$objrs = mysqli_query($con, $objsql);
$numrow = mysqli_num_rows($objrs);
$data[] = array(
        'numRow'=>$numrow,
        'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
