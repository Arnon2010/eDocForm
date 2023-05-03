<?php
require('../db.php');

$topositionId = $_GET['tposition_id'];
$mainId = $_GET['main_id'];
$moveId = $_GET['move_id'];

$objsql = "UPDATE sign_detail SET detail_status = 'D' 
    WHERE main_id = '$mainId' 
    AND tposition_id = '$topositionId'
    ANd sign_status = '0'
    ";
if(mysqli_query($con, $objsql)){
    $objsqlInsMove = "UPDATE sign_move SET move_status = '0' WHERE move_id = '$moveId'";
    if(mysqli_query($con, $objsqlInsMove)){

        $objsqlUpdDetail = "UPDATE sign_detail SET sign_status = '2' 
            WHERE main_id = '$mainId' 
            AND sign_status = '3' 
            AND detail_status = '1'
            ";
        if(mysqli_query($con, $objsqlUpdDetail))
            $status = true;
    }
}

$data[] = array(
    'status'=>$status
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));


?>