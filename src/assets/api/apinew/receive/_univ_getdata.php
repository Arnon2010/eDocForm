<?php
require('../db.php');

$objsql = "SELECT univ_id, univ_name FROM univ";
    
$objrs = mysqli_query($con, $objsql);
$i=0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $i++;
    $data[] = array(
        'Id'=>$objdata['univ_id'],
        'Name'=>$objdata['univ_name']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

//print_r(json_encode($data));
//return json_encode($data);
print json_encode(array("data"=>$data));
?>