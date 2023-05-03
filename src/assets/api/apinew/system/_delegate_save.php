<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

@$submitType = $request->submit_type;
@$delegGroupId = $request->delegGroupId;
@$delegGroupNew = $request->delegGroupNew;
@$departId = $request->departId;
@$delegateId = $request->delegateId;
@$delegateName = $request->delegateName;

if($delegGroupNew != '' && $delegGroupId == 'N'){
           
    $objsql2 = "INSERT INTO `delegate_group` (`deleg_group_name`, `depart_id`, `status`)
        VALUES ('$delegGroupNew', '$departId', '1')";
            
    if(mysqli_query($con, $objsql2)){
        
        $delegGroupId = mysqli_insert_id($con);
        
        // $objsql3 = "SELECT MAX(position_id) as position_id_max
        //     FROM position WHERE depart_id = '$departId'";
        // $objres = mysqli_query($con, $objsql3);
        // $objdata = mysqli_fetch_assoc($objres);
        
        // $positionnewId = $objdata['position_id_max'];
        // $positionId = $positionnewId;
    }
    
}

if($submitType == 'Insert'){

    //check user
    $sqlCheck = "SELECT * FROM delegate_group_user 
        WHERE citizen_id = '$delegateId' AND deleg_group_id = '$delegGroupId'";
    $resCheck = mysqli_query($con, $sqlCheck);
    $datarow = mysqli_num_rows($resCheck);


    // Add data delegate user
    if($datarow < 1) {
        $objsql = "INSERT INTO delegate_group_user(citizen_id, deleg_group_id, status)
        VALUES('$delegateId', '$delegGroupId','1')";
        if(mysqli_query($con, $objsql)){

             //check user
            $sql_chkuser = "SELECT * FROM delegate_user 
                WHERE citizen_id = '$delegateId' 
                AND depart_id = '$departId'";
            $res_chkuser = mysqli_query($con,$sql_chkuser);
            $row_chkuser = mysqli_num_rows($res_chkuser);
            
            //Add delegate_user
            if($row_chkuser == 0) {
                $sql_adduser = "INSERT INTO delegate_user SET citizen_id = '$delegateId', 
                    person_name = '$delegateName', 
                    depart_id = '$departId', 
                    status = '1'";
                mysqli_query($con, $sql_adduser);
            }
            
            $status = "true";
            $mesg = 'บันทึกข้อมูลสำเร็จ';
            $alert_color = 'text-success';
        } else {
            $status = "false";
            $mesg = 'ไม่สามารถบันทึกข้อมูลได้ !';
            $alert_color = 'text-danger';
        }
    } else {
        $mesg = 'ผู้รับมอบหมายอยู่ในกลุ่มงานนี้แล้ว !';
        $alert_color = 'text-dark';
        $status = "false";
    }
        
    
}

    
$data[] = array(
    'status'=>$status,
    'departid'=>$departId,
    'submitType'=>$submitType,
    'delegateId'=>$delegateId,
    'delegateName'=>$delegateName,
    'mesg'=>$mesg,
    'alert_color'=>$alert_color,
    'datarow'=>$datarow
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));

?>