<?php
require('../db.php');
$facCode = $_GET['fac'];
//$User = $_GET[user];
$objsql = "SELECT f.fac_bookcode,
    b.order_No,
    b.bsend_date,
    b.bsend_number,
    b.bsend_title,
    b.bsend_secret,
    b.bsend_rapid,
    b.status,
    CONCAT(m.userFname,' ',m.userLname) as userFullname
    FROM  tbbooksend_main b
    LEFT JOIN tbfaculty f ON b.fac_code = f.fac_code
    LEFT JOIN member_allow m ON b.userid = m.id
    WHERE b.fac_code = '$facCode'
    ORDER BY b.bsend_date DESC";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    if($objdata['status'] == '1'){
        $status = "ส่งแล้ว";
    }else if($objdata['status'] == '0'){
        $status = "ยังไม่ส่งถึงหน่วยงาน";
    }
    $data[] = array(
        'orderNo'=>$objdata['order_No'],
        'booksendNumber'=>$objdata['fac_bookcode'].''.$objdata['bsend_number'],
        'bsendDate'=>$objdata['bsend_date'],
        'bsendTitle'=>$objdata['bsend_title'],
        'bsendSecret'=>$objdata['bsend_secret'],
        'bsendRapid'=>$objdata['bsend_rapid'],
        'userFullname'=>$objdata['userFullname'],
        'status'=>$status
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
