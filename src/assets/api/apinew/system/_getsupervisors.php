<?php
require('../db.php');
$facCode = $_GET[faccode];

$objsql = "SELECT tposition_name, e_passport
    FROM tbtakeposition
    WHERE fac_code = '$facCode' 
    ORDER BY position_id ASC
    ";
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    $data[] = array(
        'ePassport'=>$objdata['e_passport'],
        'Name'=>$objdata['tposition_name']
    );
}
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
