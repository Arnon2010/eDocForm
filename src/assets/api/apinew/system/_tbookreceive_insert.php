<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$tbookreceiveName = $request->tbookreceiveName;
$facCode = $request->faccode;

$objsql = "INSERT INTO tbtbookreceive(tbookreceive_id, tbookreceive_name, fac_code, status)
            VALUES(null, '$tbookreceiveName','$facCode','1')";
            
if(mysqli_query($con, $objsql))
    $status = "true";
else
    $status = "false";
    
$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>