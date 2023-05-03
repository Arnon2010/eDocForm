<?php
require('../db.php');

$userEpass = $_GET['user'];

$objsqlchk ="SELECT d.depart_id, d.depart_name
    FROM edoc_user u
    LEFT JOIN department d ON u.depart_id = d.depart_id
    WHERE user_name = '$userEpass'
    AND u.depart_allow = '1'
    AND u.user_status = '1'
    AND d.depart_cancel = '0'
    AND u.depart_id IN (
        SELECT depart_id FROM department
    )";

$objrschk = mysqli_query($con, $objsqlchk);
$objdatachk = mysqli_num_rows($objrschk);
$dept = array();
if($objdatachk > 1){
    $status = 'true';
    while($objdata = mysqli_fetch_array($objrschk)){
        $dept[] = array(
          
            'Id'=>$objdata['depart_id'],
            'Name'=>$objdata['depart_name']
            
        );
    }
    
}else if($objdatachk == 0){
    $status = 'false';
}

$data[] = array(
    'check'=>$status,
    'resp'=>''
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data, "dept"=>$dept));
?>

