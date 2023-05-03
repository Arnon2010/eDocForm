<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

@$submitType = $request->submit_type;
@$delegateGroupId = $request->delegateGroupId;
@$delegateGroupName = $request->delegateGroupName;

if($submitType == 'Update'){
    $objsql = "UPDATE delegate_group SET deleg_group_name = '$delegateGroupName' 
    WHERE deleg_group_id = '$delegateGroupId'";
}

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