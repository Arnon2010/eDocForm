<?php
session_start();
require('../db.php');
$sequNo = $_GET['sequ_no'];
$mainId = $_GET['main_id'];
$signerLength = $_GET['signer_length'];
$tpositionId = $_GET['tposition_id'];

$status = 'false';

$objsql = "DELETE  FROM  sign_sequence 
    WHERE sequ_no = '$sequNo'";
if(mysqli_query($con, $objsql)){
    $status = 'true';

    // del sign move
    $objsqlMov = "DELETE FROM  sign_move  WHERE main_id = '$mainId' AND tposition_id = '$tpositionId'";
    mysqli_query($con, $objsqlMov);
    
    if($signerLength == 1){
        $objsql2 = "UPDATE sign_detail SET tposition_id = '' WHERE main_id = '$mainId'";
        mysqli_query($con, $objsql2);

        
    }

}

$data[] = array(
    'status'=>$status,
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
