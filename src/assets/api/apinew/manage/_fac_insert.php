<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

//$User = mysqli_real_escape_string($con, $data->fac);
//$Pass = mysqli_real_escape_string($con, $data->Pass);
$facCode = $request->faccode;
$facName = $request->facname;
$facId = $request->facid;

$objsql = "INSERT INTO tbfaculty(fac_id, fac_code, fac_name, parent, status)
            VALUES(null, '$facCode', '$facName','$facId','1')";
if(mysqli_query($con, $objsql)){
    $status = "true";
}else{
    $status = "false";
}
$data[] = array(
    'status'=>$status
);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>