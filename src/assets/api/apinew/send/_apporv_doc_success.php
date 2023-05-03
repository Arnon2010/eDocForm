<?php
require('../db.php');
date_default_timezone_set("Asia/Bangkok");

@$mainId = $_GET['main_id'];
@$departId = $_GET['depart_id'];
@$edocId = $_GET['edoc_id'];
@$userId = $_GET['userid'];

@$timeUpdate = date('Y-m-d H:i:s');

$status = false;

// ปรับสถานะของ sign main
$objsqlUpdMain = "UPDATE sign_main SET main_status = '4' 
    WHERE edoc_id = '$edocId' 
    AND main_id = '$mainId' 
    AND depart_id = '$departId'";
    
if(mysqli_query($con, $objsqlUpdMain)) {
    // ปรับสถานะของ sign move

    // select detail sign_status = 2

    $obj_detail = mysqli_query($con, "SELECT detail_id, tposition_id FROM sign_detail 
    WHERE main_id = '$mainId' AND detail_status = '1' AND sign_status = '2'");
    while($row_detail = mysqli_fetch_array($obj_detail)) {

    $add_move = mysqli_query($con, "INSERT INTO  sign_move SET 
        main_id = '$mainId',
        detail_id = '$row_detail[detail_id]',
        tposition_id = '$row_detail[tposition_id]',
        activity = 'DoneSent',
        user_id = '$userId',
        time = '$timeUpdate'");
    }

    // ปรับสถานะของ sign detail
    $update_detail = mysqli_query($con, "UPDATE sign_detail SET sign_status = '4'
    WHERE main_id = '$mainId' 
    AND sign_status IN ('2') 
    AND detail_status = '1'");

    if($update_detail)
        $status = true;
}



$data[] = array(
    'status'=> $status,
    'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));