<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));
$userFname = $request->userFname;
$userLname = $request->userLname;
$userEpass = preg_replace("/[^a-z . _\d]/i", '', $request->userEpass);
$userEpassOld = preg_replace("/[^a-z . _\d]/i", '', $request->userEpassOld);
$Secrets = $request->Secrets;
$privilegeEdit = $request->privilegeEdit;
$privilegeBrowsing = $request->privilegeBrowsing;
$userType = $request->userType;
$departId = $request->departId;
$Action = $request->action;
$userId = $request->userid;
$userUnique = $request->user_unique;
//$facultyCodeNew = $request->facultyCodeNew;



$objsqlchk ="SELECT user_name FROM edoc_user
    WHERE user_name = '$userEpass' AND depart_id = '$departId'";
$objrschk = mysqli_query($con, $objsqlchk);
$numrow_check = mysqli_num_rows($objrschk);

    if($Action == 'Insert'){
        if($numrow_check == 1){
            $status = 'unique'; //ผู้ใช้งานมีอยู่แล้วในหน่วยงานนี้ 
        }else{
        $objsql = "INSERT INTO edoc_user(user_id,
            user_name,
            user_fname,
            user_lname,
            privbrow_id,
            privedit_id,
            user_type,
            secrets_id,
            depart_id,
            user_status) VALUES(null,
            '$userEpass',
            '$userFname',
            '$userLname',
            '$privilegeBrowsing',
            '$privilegeEdit',
            '$userType',
            '$Secrets',
            '$departId',
            '1'
            )";
            if(mysqli_query($con, $objsql)){
                $status = "true";
                //เพิ่มผู้ใช้ใหม่ในหน่วยงานอื่น
                if($userUnique == 1){
                    $objsql = "UPDATE edoc_user SET depart_allow = '1' WHERE user_name = '$userEpass'";
                    mysqli_query($con, $objsql);
                }
            }else{
                $status = "false";
            }
        }
    }else if($Action == 'Update'){
        
        $objsqlchk ="SELECT user_name
            FROM edoc_user
            WHERE user_name = '$userEpass'
            ";
        $objrschk = mysqli_query($con, $objsqlchk);
        $numrow = mysqli_num_rows($objrschk);
        
        if($numrow >= 1 && $userEpass != $userEpassOld){
            $status = 'unique'; //ผู้ใช้งานมีอยู่แล้วในหน่วยงานนี้ 
        }else{
            $objsql = "UPDATE edoc_user SET user_name = '$userEpass',
            user_fname = '$userFname',
            user_lname = '$userLname',
            privbrow_id = '$privilegeBrowsing',
            privedit_id = '$privilegeEdit',
            user_type = '$userType',
            secrets_id = '$Secrets',
            depart_id = '$departId' WHERE user_id='$userId'
            ";
            if(mysqli_query($con, $objsql)){
                $status = "true";
            }else{
                $status = "false";
            }
        }
  
    }
    
$data[] = array(
    'status'=>$status,
    'departid'=>$departId,
    'resp'=>$numrow
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));
?>