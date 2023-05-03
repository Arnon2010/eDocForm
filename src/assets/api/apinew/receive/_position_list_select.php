<?php
require('../db.php');
$departId = $_GET['departid'];
$objsql = "SELECT position_id, position_name
    FROM position 
    WHERE depart_id = '$departId' AND status = '1'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['position_id'],
        'Name'=>$objdata['position_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
