<?php
require('../db.php');
$departId = $_GET['departid'];
$objsql = "SELECT * FROM position WHERE depart_id = '$departId'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'positionId'=>$objdata['position_id'],
        'positionName'=>$objdata['position_name'],
        'departId'=>$objdata['depart_id'],
        'Status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
