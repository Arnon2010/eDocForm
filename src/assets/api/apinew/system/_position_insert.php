<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$positionName = $request->positionName;
@$departId = $request->departid;

$objsql = "INSERT INTO `position` (`position_id`, `position_name`, `depart_id`, `status`)
    VALUES (NULL, '$positionName', '$departId', '1');";
            
if(mysqli_query($con, $objsql))
    $status = "true";
else
    $status = "false";
    
$data[] = array(
    'status'=>$status,
    'departId'=>$departId
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>