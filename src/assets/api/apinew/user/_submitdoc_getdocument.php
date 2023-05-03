<?php
require('../db.php');

$tempId = $_GET['tempid'];
/*
*/
$objsql = "SELECT t.submittemp_id, t.submittemp_docNo, t.submittemp_filename, f.fac_name FROM tbsubmit_temp t
    LEFT JOIN tbfaculty f ON t.submittemp_fac = f.fac_code
    WHERE t.submittemp_id='$tempId'
    ";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'tempId'=>$objdata['submittemp_id'],
        'docNo'=>$objdata['submittemp_docNo'],
        'fileName'=>$objdata['submittemp_filename'],
        'facName'=>$objdata['fac_name'],
    );
}


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>