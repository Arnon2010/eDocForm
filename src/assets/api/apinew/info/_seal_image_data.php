<?php
require('../db.php');
$depart_id = $_GET['depart'];

$objsql = "SELECT path_seal
    FROM department
    WHERE depart_id = '$depart_id'";
    
$objrs = mysqli_query($con, $objsql);
$numrow = mysqli_num_rows($objrs);

$objdata = mysqli_fetch_assoc($objrs);
    
    $data[] = array(
        'pathSeal'=>$objdata['path_seal'],
        'numRow'=>$numrow
    );


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
