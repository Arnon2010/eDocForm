<?php
require('../db.php');

if($_GET['usertype'] == 'SA'){
    $objsql = "SELECT * FROM tbusertype WHERE status = '1'";
}else if($_GET['usertype'] == 'A'){
    $objsql = "SELECT * FROM tbusertype WHERE usertype_code <> 'SA' AND status = '1'";
}

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['usertype_id'],
        'Name'=>$objdata['usertype_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>