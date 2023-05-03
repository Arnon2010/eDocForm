<?php
require('../db.php');
$departId = $_GET['departid'];
$objsql = "SELECT * FROM position WHERE status = '1' AND depart_id = '$departId'";
$objrs = mysqli_query($con, $objsql);

$data[] = array(
        'Id'=>'N',
        'Name'=>'[ เพิ่มตำแหน่งใหม่ ]'
    );

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
