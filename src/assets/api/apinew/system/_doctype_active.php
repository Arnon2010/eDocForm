<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$doctypeId= $request->doctype_id;
@$statusActive= $request->status_active;

if($statusActive == '0')
    $Active = '1';
else
    $Active = '0';
    
$objsql = "UPDATE edoc_type SET edoc_type_status = '$Active' WHERE edoc_type_id = '$doctypeId'";
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