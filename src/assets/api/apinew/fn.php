<?php
//date time
date_default_timezone_set("Asia/Bangkok");
$dayTH = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
$monthTH = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
$monthTH_brev = [null,'ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

function thai_date_short($time){   // 19  ธ.ค. 2556a
    global $dayTH,$monthTH_brev;   
    $thai_date_return = date("j",$time);   
    $thai_date_return.=" ".$monthTH_brev[date("n",$time)];   
    $thai_date_return.= " ".(date("Y",$time)+543);   
    return $thai_date_return;   
} 
function thai_date_fullmonth($time){   // 19 ธันวาคม 2556
    global $dayTH,$monthTH;
    $strdate = explode("-",$time);
    $thai_date_return = $strdate[2];   
    $thai_date_return.=" เดือน ".$monthTH[(date("n",$time)+1)];   
    $thai_date_return.= " พ.ศ. ".($strdate[0] + 543);   
    return $thai_date_return;   
} 


function getdocNo_send($depart){
    $objsql = "SELECT sent_no FROM edoc_sent_no WHERE depart_id='$depart'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $sentNo = $objdata['sent_no'];
    return $sentNo;
}

function getdepartCode($depart){
    $objsql = "SELECT depart_code FROM department WHERE depart_id='$depart'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $departCode = $objdata['depart_code'];
    return $departCode;
}

function getsendNo($edocid){
    $objsql = "SELECT distinct sent_no FROM edoc_sent WHERE edoc_id='$edocid'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $sentNo = $objdata['sent_no'];
    return $sentNo;
}

function getreceiveNo_first($edocid){
    $objsql = "SELECT distinct receive_no FROM edoc_receive WHERE edoc_id='$edocid'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $No = $objdata['receive_no'];
    return $No;
}

function getDepartment($departid){
    include '../db.php';
    $objsql = "SELECT depart_name, depart_parent FROM department WHERE depart_id='$departid'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $deptName = $objdata['depart_name'];
    $deptParent = $objdata['depart_parent'];
    return array($deptName, $deptParent);
}

function getDepartmentClass($departid){
    list($deptName, $deptParent) = getDepartment($departid);

    if($deptParent == '0'){
        $value= $deptName;
    }
    else{
        list($deptName2, $deptParent2) = getDepartment($deptParent);
        if($deptParent2 == '0')
            $value = $deptName2.' /'.$deptName;
        else{
            list($deptName3, $deptParent3) = getDepartment($deptParent2);
            if($deptParent3 == '0')
                $value = $deptName3.' /'.$deptName2.' /'.$deptName;
            else{
                list($deptName4, $deptParent4) = getDepartment($deptParent3);
                $value = $deptName4.' /'.$deptName3.' /'.$deptName2.' /'.$deptName;
            }
        }
    }
    return $value;
}

function getDepartmentParent($departid){
    include '../db.php';
    $objsql = "SELECT depart_id, depart_parent FROM department WHERE depart_id='$departid'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $deptId = $objdata['depart_id'];
    $deptParent = $objdata['depart_parent'];
    return array($deptId, $deptParent);
}

function getDepartmentIdCore($departid){
    list($deptId, $deptParent) = getDepartmentParent($departid);

    if($deptParent == '0'){
        $value= $deptId;
    }
    else{
        list($deptId2, $deptParent2) = getDepartmentParent($deptParent);
        if($deptParent2 == '0')
            $value = $deptId2;
        else{
            list($deptId3, $deptParent3) = getDepartmentParent($deptParent2);
            if($deptParent3 == '0')
                $value = $deptId3;
            else{
                list($deptId4, $deptParent4) = getDepartmentParent($deptParent3);
                if($deptParent4 == '0')
                    $value = $deptId4;
                else{
                    list($deptId5, $deptParent5) = getDepartmentParent($deptParent4);
                    $value = $deptId5;
                }
            }
        }
    }
    return $value;
}
//สถานะการรับหนังสือจากการส่งหนังสือ
function getStatusReceivedReturn($edocid, $departid_receive){
    include '../db.php';
    $objsql = "SELECT sent_status FROM edoc_sent 
        WHERE edoc_id='$edocid' AND depart_id = '$departid_receive' AND sent_status = '5'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $sentStatus = $objdata['sent_status'];
   
    return $sentStatus;
}

//แสดงหน่วยงานที่จะส่งกลับหนังสือ
function getDeptSendReturn($edocid, $departid){
    include '../db.php';
    $objsql = "SELECT depart_id_send FROM edoc_receive 
        WHERE edoc_id='$edocid' AND depart_id = '$departid'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $deptId = $objdata['depart_id_send'];
    $deptName = getDepartmentClass($deptId);
    
   
    return array($deptId, $deptName);
}

