<?php
require('../db.php');

$objsql = "SELECT * FROM edoc_type";
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'No'=>$i,
        'doctype_id'=>$objdata['edoc_type_id'],
        'doctype_name'=>$objdata['edoc_type_name'],
        'Status'=>$objdata['edoc_type_status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>