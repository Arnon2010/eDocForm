<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$userId = $_POST["user_id"];//รหัสผู้ใช้
@$mainId = $_POST['main_id'];//รหัส sign main
@$detailId = $_POST['detail_id']; // sign detail id old

@$moddate = date('Y-m-d H:i:s');

//$filePath = $_GET['path'];

/* Location */
@$location = '../../document';

//@$Path = $location.$filePath;

$sql_detail = "SELECT sign_status FROM sign_detail WHERE detail_id = '$detailId' AND main_id = '$mainId'";
$res_detail = mysqli_query($con, $sql_detail);
$row_detail = mysqli_fetch_array($res_detail);

$sign_status = $row_detail['sign_status'];

if($sign_status == '5'){

    // $sql_del = "DELETE FROM sign_detail WHERE detail_id='$detailId'";
    // if(mysqli_query($con, $sql_del)){
    //     @unlink($Path);
    //     @$status = 'true'; 
    // }else{
    //     @$status = 'false';
    // }

    $update_detail = "UPDATE sign_detail SET detail_status = 'D'
        WHERE detail_id='$detailId' AND main_id = '$mainId'";
    if(mysqli_query($con, $update_detail)) {
        @$status = 'true';
    }else{
        @$status = 'false';
    }

    $upd_move = mysqli_query($con, "UPDATE sign_move SET move_status = 'D', user_id = '$userId', time = '$moddate'
    WHERE detail_id = '$detailId' AND main_id = '$mainId'");
    
}else{
    if($sign_status == '1') //ส่งหนังสือแล้ว
        $status = 1;
    else if($sign_status == '2'){//รอส่งต่อหรือออกเลข
        $status = 2;
    }
    else if($sign_status == '3'){//มีการส่งต่อหนังสือ
        $status = 3;
    }
}


$data[] = array(
    'status'=> $status,
    'resp'=>''
    );

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));