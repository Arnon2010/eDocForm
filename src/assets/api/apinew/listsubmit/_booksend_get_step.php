<?php
/**/
require('../db.php');
$orderNo = $_GET[order_no];
$facCode = $_GET[faccode];

$objsql = "SELECT
        m.bsend_id,
        m.order_No,
        m.bsend_number,
        m.bsend_date,
        m.status,
        m.fac_code,
        t.tbooksend_name,
        t.tbooksend_id
    FROM tbbooksend_main m
    LEFT JOIN tbtbooksend t ON m.tbooksend_id = t.tbooksend_id
    WHERE  bsend_id in (SELECT MAX(m.bsend_id) FROM tbbooksend_main m)
    AND m.order_No = '$orderNo' AND m.fac_code = '$facCode'";
    
$objrs = mysqli_query($con, $objsql);
$num_row = mysqli_num_rows($objrs);
if($num_row == 1){
    while($objdata = mysqli_fetch_assoc($objrs)){
    $data[] = array(
    'status'=>$objdata['status'],
    'bsendId'=> $objdata['bsend_id'],
    'orderNo'=> $objdata['order_No'],
    'tbooksendName'=> $objdata['tbooksend_name'],
    'bsendNumber'=> $objdata['bsend_number'],
    'bsendDate'=> $objdata['bsend_date'],
    'facCode'=> $objdata['fac_code'],
    'tbooksendId'=> $objdata['tbooksend_id']
    );
}
}else{
    $data[] = array('status'=>'0');
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
