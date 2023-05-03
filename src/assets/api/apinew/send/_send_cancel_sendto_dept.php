<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

@$sentId = $request->sentId;
@$departId = $request->departId;
@$edocId = $request->edocId;
@$docStatus = $request->doc_status;

if($docStatus == 'C')
    $sentStatus = 'C';
else{
    $objsql_r = "SELECT * FROM edoc_receive WHERE edoc_id = '$edocId' AND depart_id = '$departId'";
    $objres_r = mysqli_query($con, $objsql_r);
    $numrow_r = mysqli_num_rows($objres_r);
        
    if($numrow_r == 1)//กรณีที่มีการลงรับแล้ว
        $sentStatus = '2';
    else
        $sentStatus = 1;
}

    
$sql_del = "UPDATE edoc_sent SET sent_status='$sentStatus' WHERE sent_id='$sentId'";
if(mysqli_query($con, $sql_del)){
    $objsql2 = "UPDATE edoc_receive SET status = '$docStatus' WHERE edoc_id='$edocId' AND depart_id='$departId'";
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