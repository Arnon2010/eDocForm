<?php
require('../db.php');
$facCode = $_GET[faccode];
$User = $_GET[user];
if($_GET[action] == 'cancel'){
    $Order = $_GET[order];
    $cond = " AND order_No = '$Order'";
}

$objsql = "SELECT submitmain_id, order_No, submitmain_date, submitmain_title, submitmain_note, status
    FROM tbsubmit_main
    WHERE faccode = '$facCode' AND user = '$User' $cond
    ORDER BY datemodified DESC";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs) ){
    $status_cancel = 0;
    if($objdata['status'] == '1'){
        $status = "รอตรวจสอบ";
        $status_cancel = 1;
    }elseif($objdata['status'] == '2'){
        $status = "รอพิจารณา";
    }elseif($objdata['status'] == '3'){
        $status = "อนุมัติแล้ว";
    }elseif($objdata['status'] == '4'){
        $status = "กำลังดำเนินการ";
    }elseif($objdata['status'] == '5'){
        $status = "ส่งหนังสือเรียบร้อย";
    }
    $data[] = array(
        'mainId'=>$objdata['submitmain_id'],
        'Order'=>$objdata['order_No'],
        'Date'=>$objdata['submitmain_date'],
        'Title'=>$objdata['submitmain_title'],
        'Note'=>$objdata['submitmain_note'],
        'status'=>$status,
        'statusCancel'=>$status_cancel
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
