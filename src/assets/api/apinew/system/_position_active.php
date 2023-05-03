<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$positionId = $request->position_id;
@$statusActive= $request->status_active;

if($statusActive == 'D')
    $Active = '1';
else
    $Active = 'D';
    
$objsql = "UPDATE position SET status = '$Active' WHERE position_id = '$positionId'";
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