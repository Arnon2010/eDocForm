<?php
session_start();
require('../db.php');
$sequNo = $_GET['sequ_no'];
$mainId = $_GET['main_id'];
$tpositionId = $_GET['tposition_id'];

$status = 'false';

$num_row = mysqli_num_rows(mysqli_query($con, "SELECT detail_id FROM sign_detail 
    WHERE main_id = '$mainId' 
    AND tposition_id = '$tpositionId' 
    AND detail_status = '1'
    ")
);

if($num_row == 0){
    $objsql = "DELETE  FROM  sign_sequence 
    WHERE sequ_no = '$sequNo'";
    if(mysqli_query($con, $objsql)){
        $status = 'true';
    }
}else{
    $msg = 'มีการส่งต่อหนังสือแล้วไม่สามารถลบได้ !';
    $status = 'true';
}



$data[] = array(
    'status'=>$status,
    'msg'=>$msg
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
