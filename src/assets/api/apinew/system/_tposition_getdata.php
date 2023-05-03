<?php
require('../db.php');
$departId = $_GET['departid'];
$objsql = "SELECT t.tposition_id, t.tposition_name, p.position_id, p.position_name, t.e_passport, p.depart_id, t.status
    FROM takeposition t
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE p.depart_id = '$departId'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'takepId'=>$objdata['tposition_id'],
        'takepName'=>$objdata['tposition_name'],
        'positionId'=>$objdata['position_id'],
        'positionName'=>$objdata['position_name'],
        'ePassport'=>$objdata['e_passport'],
        'departId'=>$objdata['depart_id'],
        'status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
