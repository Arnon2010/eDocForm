<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$tpositionId = $request->tpositionId;

$objsql = "DELETE FROM tbtakeposition WHERE tposition_id = '$tpositionId'";
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