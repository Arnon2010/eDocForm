<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$departId = $request->departid;

$objsql = "DELETE FROM department WHERE depart_id = '$departId'";
if(mysqli_query($con, $objsql)){
    $data[] = array(
        'status'=>'true'
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>