<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$doctypeName = $request->doctypeName;
$doctypeId = $request->doctypeId;
$submitType = $request->submitType;
if($submitType == 'Insert'){
    $objsql = "INSERT INTO edoc_type(edoc_type_id, edoc_type_name, edoc_type_status)
            VALUES(null, '$doctypeName','1')";
}else{
    $objsql = "UPDATE edoc_type SET
        edoc_type_name = '$doctypeName'
        WHERE edoc_type_id = '$doctypeId'";
}

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