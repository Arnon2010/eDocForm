<?php
session_start();
require('../db.php');
$titleDoc = $_POST['titledoc'];
$facCode = $_POST['faccode'];
$userId = $_POST['userid'];
$orderNo = $_POST['orderno'];
//$bsendDate = $_POST['bsenddate'];
$bsendDate = date('Y-m-d');
$tbooksendId = $_POST['tbooksendid'];
$Secret = $_POST['secret'];
$Rapid = $_POST['rapid'];
$Note = $_POST['note'];

$objsql = "SELECT nsend_no FROM tbnsend WHERE fac_code='$facCode' AND tbooksend_id = '$tbooksendId'";
$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_array($objrs);
$numrow = mysqli_num_rows($objrs);

if($numrow == 0){
    $numberSend =  1;
}else{
    $numberSend = $objdata['nsend_no'] + 1;
}

// Insert to table tbtemp_offerbooksend
$objsql = "INSERT INTO tbbooksend_main(
    bsend_id,
    bsend_title,
    order_No,
    bsend_number,
    bsend_date,
    bsend_secret,
    bsend_rapid,
    bsend_note,
    tbooksend_id,
    fac_code,
    userid,
    status
    )
VALUES(
    null,
    '$titleDoc',
    '$orderNo',
    '$numberSend',
    '$bsendDate',
    '$Secret',
    '$Rapid',
    '$Note',
    '$tbooksendId',
    '$facCode',
    '$userId',
    '1')";
if(mysqli_query($con, $objsql)){
    if($numrow == 0){
        $objsql2 = "INSERT INTO tbnsend(nsend_id, fac_code, tbooksend_id, nsend_no, status)
            values(
            null,'$facCode', '$tbooksendId', '$numberSend', '1'
            )";
    }else{
        $objsql2 = "UPDATE tbnsend SET nsend_no='$numberSend' WHERE fac_code='$facCode' AND tbooksend_id = '$tbooksendId'";
    }
    mysqli_query($con, $objsql2);
    /*
    $objsql = "SELECT MAX(bsend_id) as bsendId FROM tbbooksend_main
    WHERE order_No = '$orderNo'
    AND fac_code = '$facCode'";
    */
    $objsql = "SELECT m.bsend_id, m.order_No, t.tbooksend_name, t.tbooksend_id, m.bsend_number, m.bsend_date, m.fac_code
        FROM tbbooksend_main m
        LEFT JOIN tbtbooksend t ON m.tbooksend_id = t.tbooksend_id
        WHERE  bsend_id in (SELECT MAX(m.bsend_id) FROM tbbooksend_main m)
        AND m.order_No = '$orderNo'
        AND m.fac_code = '$facCode'";
        
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_array($objrs);
    $status = 'true';

    $objsql = "UPDATE tbsubmit_main SET status = '4' WHERE order_No='$orderNo' AND faccode = '$facCode'";
    mysqli_query($con, $objsql);
}else{
    $status = 'false';
}
$arr = array(
    'status'=> $status,
    'bsendId'=> $objdata['bsend_id'],
    'orderNo'=> $objdata['order_No'],
    'tbooksendName'=> $objdata['tbooksend_name'],
    'bsendNumber'=> $objdata['bsend_number'],
    'bsendDate'=> $objdata['bsend_date'],
    'facCode'=> $objdata['fac_code'],
    'tbooksendId'=> $objdata['tbooksend_id']
);
echo json_encode($arr);
