<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

//$User = mysqli_real_escape_string($con, $data->fac);
//$Pass = mysqli_real_escape_string($con, $data->Pass);
@$departCode = $request->departcode;
@$departName = $request->departname;
@$departId = $request->departid;
@$univId = $request->univid;
    
$objsql = "INSERT INTO department(depart_id, depart_name, depart_parent, univ_id, depart_cancel, depart_code)
            VALUES(null, '$departName', '$departId', '$univId', '0', '$departCode')";
if(mysqli_query($con, $objsql)){
    if($departId != 0){
        $objsql2 = "SELECT depart_id FROM department WHERE depart_name = '$departName'";
        $objres2 = mysqli_query($con,$objsql2);
        $objdata2 = mysqli_fetch_array($objres2);
        @$depart_id_under = $objdata2[depart_id];
        $objsql3 = "INSERT INTO department_hier(head_depart_id, under_depart_id)
            VALUES('$departId', '$depart_id_under')";
        mysqli_query($con, $objsql3);
    }
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