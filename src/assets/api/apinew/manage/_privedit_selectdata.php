<?php
require('../db.php');

$objsql = "SELECT * FROM tbprivedit WHERE status = '1'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['privedit_id'],
        'Name'=>$objdata['privedit_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>