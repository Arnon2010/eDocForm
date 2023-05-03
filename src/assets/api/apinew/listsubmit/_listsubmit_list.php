<?php
require('../db.php');
$facCode = $_GET[faccode];
//$User = $_GET[user];
$objsql = "SELECT s.submitmain_id, s.order_No, submitmain_date, submitmain_title, datemodified, s.status, m.userFname, m.userLname
    FROM tbsubmit_main s
    LEFT JOIN member_allow m ON s.user = m.id
    WHERE s.faccode = '$facCode'
    ORDER BY s.datemodified DESC";
    
$objrs = mysqli_query($con, $objsql);

$statusSubmit = 0;//รอตรวจสอบ
$statusOffer = 0;//รอพิจารณา

while($objdata = mysqli_fetch_assoc($objrs) ){
    if($objdata['status'] == '1'){
        $status = "รอตรวจสอบ";
        $statusSubmit = 1;
        $statusOffer = 0;
        $statusConfirm = 0;
        $statusProgress = 0;
        $statusSuccess = 0;
    }else if($objdata['status'] == '2'){
        $status = "รอพิจารณา";
        $statusOffer = 1;
        $statusSubmit = 0;
        $statusConfirm = 0;
        $statusProgress = 0;
        $statusSuccess = 0;
    }else if($objdata['status'] == '3'){
        $status = "อนุมัติแล้ว (รอส่ง)";
        $statusOffer = 0;
        $statusSubmit = 0;
        $statusConfirm = 1;
        $statusProgress = 0;
        $statusSuccess = 0;
    }else if($objdata['status'] == '4'){
        $status = "กำลังดำเนินการส่งหนังสือ";
        $statusOffer = 0;
        $statusSubmit = 0;
        $statusConfirm = 0;
        $statusProgress = 1;
        $statusSuccess = 0;
    }else if($objdata['status'] == '5'){
        $status = "ส่งหนังสือแล้ว";
        $statusOffer = 0;
        $statusSubmit = 0;
        $statusConfirm = 0;
        $statusProgress = 0;
        $statusSuccess = 1;
    }
    $data[] = array(
        'mainId'=>$objdata['submitmain_id'],
        'orderNo'=>$objdata['order_No'],
        'Date'=>$objdata['submitmain_date'],
        'Title'=>$objdata['submitmain_title'],
        'dateModified'=>$objdata['datemodified'],
        'fullName'=>$objdata['userFname'].' '.$objdata['userLname'],
        'status'=>$status,
        'statusSubmit'=>$statusSubmit,
        'statusOffer'=>$statusOffer,
        'statusConfirm'=>$statusConfirm,
        'statusProgress'=>$statusProgress,
        'statusSuccess'=>$statusSuccess
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
