<?php
session_start();
require('../db.php');
$bsendId = $_POST['bsendid'];
$userAdd = $_POST['userid'];
$orderNo = $_POST['orderno'];
$facCode = $_POST['faccode'];
$status = 'false';

$objsql = "SELECT
    n.isunumber_filepath,
    n.isunumber_filename,
    n.order_No,
    d.fac_receive,
    d.offerdoc_prefer
    FROM tbissuenumber_temp n
    LEFT JOIN tbofferbooksend_doc d ON n.offerdoc_id = d.offerdoc_id
    LEFT JOIN tbofferbooksend_main o ON d.offermain_id = o.offermain_id
    WHERE n.order_No = '$orderNo'
    AND o.faccode ='$facCode'";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_array($objrs) ){
    
    $objsqlDetail = "INSERT INTO tbbooksend_detail(
        booksenddetail_id,
        bsend_id,
        booksenddetail_prefer,
        booksenddetail_filepath,
        booksenddetail_filename,
        datesend,
        fac_receive,
        status
        )
    VALUES(
        null,
        '$bsendId',
        '$objdata[offerdoc_prefer]',
        '$objdata[isunumber_filepath]',
        '$objdata[isunumber_filename]',
        '".date('Y-m-d h:i:s')."',
        '$objdata[fac_receive]',
        'W'
       )";
    if(mysqli_query($con, $objsqlDetail)){
        $status = 'true';
        
    }else{
        $status = 'false';
    }
}
if($status == 'true'){
    ## status == '2' is Document send success. ##
    $objsqlMain = "UPDATE tbbooksend_main SET status = '2' WHERE order_No = '$orderNo' AND fac_code = '$facCode'";
    mysqli_query($con, $objsqlMain);
    
    $objsqlsubmitMain = "UPDATE tbsubmit_main SET status = '5' WHERE order_No = '$orderNo' AND faccode = '$facCode'";
    mysqli_query($con, $objsqlsubmitMain);
    
    $objsqlDelTemp = "DELETE FROM tbissuenumber_temp WHERE order_No = '$orderNo' AND faccode = '$facCode'";
    mysqli_query($con, $objsqlDelTemp);
}
$arr = array(
    'status'=> $status
);
echo json_encode($arr);
