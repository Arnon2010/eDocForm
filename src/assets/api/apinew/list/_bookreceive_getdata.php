<?php
require('../db.php');
$facCode = $_GET['fac'];

//$User = $_GET[user];
$objsql = "SELECT d.booksenddetail_id as booksendId,
    f.fac_bookcode,
    b.order_No,
    b.bsend_date,
    b.bsend_number,
    b.bsend_title,
    b.bsend_secret,
    b.bsend_rapid,
    d.status,
    f.fac_name,
    CONCAT(m.userFname,' ',m.userLname) as userFullname
    FROM tbbooksend_detail d
    LEFT JOIN tbbooksend_main b ON d.bsend_id = b.bsend_id
    LEFT JOIN tbfaculty f ON b.fac_code = f.fac_code
    LEFT JOIN member_allow m ON b.userid = m.id
    WHERE d.fac_receive = '$facCode'
    ORDER BY b.bsend_date DESC";
    
$objrs = mysqli_query($con, $objsql);
$No = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $No++;
    if($objdata['status'] == 'W'){
        $statusConfirm = "ยังไม่ลงรับหนังสือ";
    }else if($objdata['status'] == 'C'){
        $statusConfirm = "ลงรับหนังสือแล้ว";
    }
    $data[] = array(
        'No'=>$No,
        'booksendId'=>$objdata['booksendId'],
        'orderNo'=>$objdata['order_No'],
        'booksendNumber'=>$objdata['fac_bookcode'].''.$objdata['bsend_number'],
        'bsendDate'=>$objdata['bsend_date'],
        'bsendTitle'=>$objdata['bsend_title'],
        'bsendSecret'=>$objdata['bsend_secret'],
        'bsendRapid'=>$objdata['bsend_rapid'],
        'userFullname'=>$objdata['userFullname'],
        'facultySend'=>$objdata['fac_name'],
        'Status'=>$objdata['status'],
        'statusConfirm'=>$statusConfirm,
    );
}

//$data[] = array('sqltest'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
