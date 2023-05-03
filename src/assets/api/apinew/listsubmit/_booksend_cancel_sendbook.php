<?php
session_start();
require('../db.php');

$bsendid = $_POST['bsendid'];
$orderNo = $_POST['orderno'];
$facCode = $_POST['faccode'];
$tbookSend = $_POST['tbooksend'];

$location = '../../document';

$objsql = "DELETE FROM tbbooksend_main WHERE bsend_id = '$bsendid' AND order_No = '$orderNo' AND fac_code = '$facCode'";
if(mysqli_query($con, $objsql)){
    $status = 'true';
    //update tbnsend
    $objsql = "SELECT nsend_no FROM tbnsend WHERE fac_code='$facCode' AND tbooksend_id='$tbookSend'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_array($objrs);
    
    $nsendNo = $objdata['nsend_no']-1;
    
    $objsqlUpd = "UPDATE tbnsend SET nsend_no='$nsendNo' WHERE fac_code='$facCode' AND tbooksend_id='$tbookSend'";
    mysqli_query($con, $objsqlUpd);
    
    $sql_main = "UPDATE tbsubmit_main SET status = '3' WHERE faccode='$facCode' AND order_No = '$orderNo'";
    mysqli_query($con, $sql_main);
    
    $sql_temp = "SELECT isunumber_filepath FROM tbissuenumber_temp WHERE order_No = '$orderNo' AND faccode = '$facCode'";
    $res_temp = mysqli_query($con, $sql_temp);
    while($row_temp = mysqli_fetch_array($res_temp)){
        $path = $row_temp[isunumber_filepath];
        $filePath = $location.$path;
        unlink($filePath);
    }
    $sql_deltemp = "DELETE FROM tbissuenumber_temp WHERE order_No = '$orderNo' AND faccode = '$facCode'";
    mysqli_query($con, $sql_deltemp);
    
}else{
    $status = 'false';
}

$arr = array(
    'status'=> $status
    
);

echo json_encode($arr);
