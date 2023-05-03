<?php
/**/
require('../db.php');
$orderNo = $_GET['orderno'];
$facCode = $_GET['faccode'];
$writeTime = $_GET[wtime];//เวลาเขียนเลขและวันที่ส่งหนังสือ
//$User = $_GET[user];
$objsql = "SELECT
    s.submitmain_title
    FROM tbsubmit_main s
    LEFT JOIN tbofferbooksend_main o ON s.order_No = o.order_No
    WHERE s.order_No = '$orderNo' AND o.faccode = '$facCode'
    AND o.status = 'C'
    GROUP BY o.order_No";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    
    $data[] = array(
        'orderNo'=>$orderNo,
        'Title'=>$objdata['submitmain_title'],
        'Prefer'=>$objdata['offer_prefer']
    );
}
//$data[] = array('sqltest'=>$objsql);
header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
