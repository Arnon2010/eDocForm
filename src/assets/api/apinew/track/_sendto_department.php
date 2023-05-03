<?php
require('../db.php');
require('../fn.php');

@$edocId = $_GET['edocid'];
/*
*/
$objsql = "SELECT d.depart_id, d.depart_name, s.receive_dept
    FROM edoc_sent s
    LEFT JOIN department d ON s.depart_id = d.depart_id
    WHERE s.edoc_id='$edocId'
    ";

$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    
    $departName = getDepartmentClass($objdata['depart_id']);
    
    $data[] = array(
        'departId'=>$objdata['depart_id'],
        'departName'=>$departName,
        'receiveDept'=>$objdata['receive_dept'],
    );
}

//$data[] = array('sql'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>