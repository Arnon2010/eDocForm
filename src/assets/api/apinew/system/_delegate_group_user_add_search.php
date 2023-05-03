<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

@$personId = $request->personId;
@$delegateGroupId = $request->delegateGroupId;
@$personName = $request->personName;
@$departId = $request->departId;

//check user
$sql_chkuser_ingroup = "SELECT * FROM delegate_group_user 
    WHERE citizen_id = '$personId' 
    AND deleg_group_id = '$delegateGroupId'";
$res_chkuser_ingroup = mysqli_query($con, $sql_chkuser_ingroup);
$row_chkuser_ingroup = mysqli_num_rows($res_chkuser_ingroup);

// Add user in group
if($row_chkuser_ingroup == 0) {
    $objsql = "INSERT INTO delegate_group_user(citizen_id, deleg_group_id, status)
    VALUES('$personId', '$delegateGroupId','1')";

    if(mysqli_query($con, $objsql)) {
        $status = "true";
        //check user
        $sql_chkuser = "SELECT * FROM delegate_user WHERE citizen_id = '$personId'";
        $res_chkuser = mysqli_query($con,$sql_chkuser);
        $row_chkuser = mysqli_num_rows($res_chkuser);
        
        //Add delegate_user
        if($row_chkuser == 0) {
            $sql_adduser = "INSERT INTO delegate_user SET citizen_id = '$personId', 
                person_name = '$personName', 
                depart_id = '$departId', 
                status = '1'";
            mysqli_query($con, $sql_adduser);
        }

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

    
$data[] = array(
    'status'=>$status,
    'delegateGroupId'=>$delegateGroupId,
    'objsql'=>'',
    'mesg'=>$mesg,
    'alert_color'=>$alert_color
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));

?>