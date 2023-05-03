<?php
require('../db.php');
//$departId = $_GET['departid'];
$positionId = $_GET['positionid'];
$objsql = "SELECT t.tposition_id, t.tposition_name
    FROM takeposition t
    LEFT JOIN position p ON t.position_id = p.position_id
    WHERE p.position_id = '$positionId' AND t.status = '1'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['tposition_id'],
        'Name'=>$objdata['tposition_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
