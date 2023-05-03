<?php
require('../db.php');

$departId = $_GET[departid];

$objsql = "SELECT t.e_passport, t.position_id, t.tposition_id, t.tposition_name, p.position_name 
FROM takeposition t 
LEFT JOIN position p ON t.position_id = p.position_id
WHERE t.status = '1' 
AND p.depart_id = '$departId'";

$objrs = mysqli_query($con, $objsql);

$data[] = array('Id'=>'','Name'=>'เลือกผู้บังคับบัญชา');

while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['tposition_id'],
        'Name'=>$objdata['tposition_name'].' ('.$objdata['position_name'].')'
    );
    
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>