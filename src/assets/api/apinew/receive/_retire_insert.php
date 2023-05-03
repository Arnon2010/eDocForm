<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
date_default_timezone_set("Asia/Bangkok");
@$retireText = $request->retiretext;
@$retireDateText = $request->retiredate;
@$tpositionId = $request->tpositionid;
@$edocId = $request->edocid;
@$userId = $request->userid;
@$departId = $request->departid;
@$retireDate = date('Y-m-d');
@$retireTime = date('H:i:s');
@$filePathRetire = $request->filePathRetire;
@$itemPersonDelegate = $request->itemPersonDelegate;

$dateArray = explode("/",$retireDateText);
$retireDateTextNew = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];

$objsql = "INSERT INTO retire (retire_id, edoc_id, tposition_id, retire_text, retire_date)
    VALUES (NULL, '$edocId', '$tpositionId', '$retireText', '$retireDateTextNew');";
            
if(mysqli_query($con, $objsql)){

    $retireId = mysqli_insert_id($con);

    ## ผู้ได้รับมอบหมาย
    foreach($itemPersonDelegate as $value) {
        // check user ซ้ำ
        $objcheck_user = "SELECT deleg_user_id FROM delegate_user 
            WHERE depart_id = '$departId' 
            AND citizen_id = '$value->Id'";
        $objres_user = mysqli_query($con, $objcheck_user);
        $row_user = mysqli_num_rows($objres_user);
        $data_user = mysqli_fetch_array($objres_user);
        $delegateUserId = $data_user['deleg_user_id'];
        //มีข้อมูลผู้รับมอบหมายอยู่แล้ว
        if($row_user == 0) {
            $objinsDelegate = "INSERT INTO delegate_user SET citizen_id='$value->Id', 
                person_name = '$value->PersonName', 
                depart_id = '$value->DepartId'";
            mysqli_query($con, $objinsDelegate);
            $delegateUserId = mysqli_insert_id($con);
        }//ยังไม่มีผู้รับมอบหมาย
        
        $objinsDelegateRetire = "INSERT INTO delegate_retire SET retire_id='$retireId', 
            deleg_user_id = '$delegateUserId',
            file_path = '$filePathRetire',
            retire_date = '$retireDate',
            retire_time = '$retireTime'";
        mysqli_query($con, $objinsDelegateRetire);

    }
    
    $status = "true";
    ## edoc track and log ##
    $operation = "เกษียนหนังสือ";
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $objsql_track = "INSERT INTO edoc_track (track_id, edoc_id, track_time, track_date, operation, depart_id, user_id, ip, status)
        VALUES(null, '$edocId', '$retireTime', '$retireDate', '$operation', '$departId', '$userId', '$ip_addr', '4')";
    mysqli_query($con, $objsql_track);
}
else
    $status = "false";
    
$data[] = array(
    'status'=>$status,
    'edocId'=>$edocId,
    'objinsDelegateRetire'=>$objinsDelegateRetire
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>