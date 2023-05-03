<?php
require('../db.php');

$objsql = "SELECT * FROM edoc_type WHERE edoc_type_status = '1'";
$objrs = mysqli_query($con, $objsql);
$i=0;

$data[] = array(
        'Id'=>'',
        'Name'=>'ทั้งหมด'
    );

while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['edoc_type_id'],
        'Name'=>$objdata['edoc_type_name']
    );
}

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>