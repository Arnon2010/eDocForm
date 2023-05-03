<?php
require('../db.php');

$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");

@$mainId = $request->mainid;//รหัส sign main
@$Activity = $request->activity;//Activity

/* กรณีมีการเสนอ แต่หนังสือได้เสร็จสิ้น (ฺBug 4/012564) */
if($Activity == 'Upload') {
    $objsql_main = "UPDATE sign_main SET main_status = '1' WHERE main_id = '$mainId'";
    if(mysqli_query($con, $objsql_main)) {
        $status = true;
    }
}
else if($Activity == 'Done'){
    $objsql_main = "UPDATE sign_main SET main_status = '2' WHERE main_id = '$mainId'";
    mysqli_query($con, $objsql_main);

    $objsql = "UPDATE sign_detail SET sign_status = '2' 
        WHERE main_id = '$mainId' 
        AND sign_status = '4'
        ";
    if(mysqli_query($con, $objsql)){
        $objsql_move = "DELETE FROM sign_move 
            WHERE main_id = '$mainId' AND activity = 'Done'";
        if(mysqli_query($con, $objsql_move)){
            $status = true;
        }
    }
}else if($Activity == 'DoneOriginal') {
    $objsql_main = "UPDATE sign_main SET main_status = '0' WHERE main_id = '$mainId'";
    mysqli_query($con, $objsql_main);

    $objsql_move = "DELETE FROM sign_move 
        WHERE main_id = '$mainId' AND activity = 'DoneOriginal'";
    if(mysqli_query($con, $objsql_move)){
        $status = true;
    }
}



$data[] = array(
    'status'=>$status,
    'Activity'=>$objsql_main
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));


?>