<?php
require('../db.php');
$facCode= $_GET['fac'];
$objsql = "SELECT * FROM tbtbooksend WHERE status = '1' AND fac_code = '$facCode'";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $data[] = array(
        'Id'=>$objdata['tbooksend_id'],
        'Name'=>$objdata['tbooksend_name'],
        'facultyCode'=>$objdata['fac_code'],
        'Status'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
