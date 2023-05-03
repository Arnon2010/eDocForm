<?php
require('../db.php');
$departId = $_GET['departid'];

$objsql = "SELECT * FROM delegate_group WHERE depart_id = '$departId'";
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['deleg_group_id'],
        'Name'=>$objdata['deleg_group_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>