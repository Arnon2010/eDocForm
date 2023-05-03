<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$edocId = $request->edocId;
/* Location */

$objsql = "UPDATE edoc SET status = 'C' WHERE edoc_id='$edocId'";
if(mysqli_query($con, $objsql)){
    
    $objsql1 = "UPDATE edoc_sent SET sent_status = 'C' WHERE edoc_id='$edocId'";
    mysqli_query($con, $objsql1);
    
    $objsql2 = "UPDATE edoc_receive SET status = 'C' WHERE edoc_id='$edocId'";
    mysqli_query($con, $objsql2);
    
    $status = 1; 
}else{
    $status = 0;
}

$data[] = array(
    'status'=> $status
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));