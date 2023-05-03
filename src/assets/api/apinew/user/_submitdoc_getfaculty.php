<?php
require('../db.php');

$tempId = $_GET['tempid'];
/*
*/
$objsql = "SELECT fac_code, fac_name 
        FROM tbfaculty 
        WHERE fac_code NOT IN
            (
                SELECT submittemp_fac
                FROM tbsubmit_temp
                WHERE submittemp_id = '$tempId'
            )";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['fac_code'],
        'Name'=>$objdata['fac_name']
    );
}


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>