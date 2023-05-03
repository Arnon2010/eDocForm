<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
@$ePassport = $request->e_passport;
@$departId = $request->depart_id;
@$managerName = $request->manager_name;
@$submitType = $request->submittype;
@$acknowId = $request->acknow_id;

if($submitType == 'Insert'){
    $objsql = "INSERT INTO department_acknow(id, depart_id, e_passport, Name, status)
                VALUES(null, '$departId', '$ePassport', '$managerName', '1')";
    
    if(mysqli_query($con, $objsql)){
        $status = "true";
    }else{
        $status = "false";
    }
}else{
                   
    $objsql = "UPDATE department_acknow SET
        depart_id = '$departId',
        e_passport = '$ePassport',
        Name = '$managerName'
        WHERE id = '$acknowId'
        ";
    
    if(mysqli_query($con, $objsql)){
        $status = "true";
    }else{
        $status = "false";
    }
}

$data[] = array(
    'status'=>$status,
    'resp'=>$objsql
);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>