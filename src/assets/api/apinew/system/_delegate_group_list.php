<?php
require('../db.php');
$departId = $_GET['departid'];
$objsql = "SELECT * FROM delegate_group WHERE status = '1' AND depart_id = '$departId'";
$objrs = mysqli_query($con, $objsql);

$data[] = array(
        'Id'=>'N',
        'Name'=>'>> กลุ่มอื่น ๆ (เพิ่มกลุ่มใหม่)'
    );

while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['deleg_group_id'],
        'Name'=>$objdata['deleg_group_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
