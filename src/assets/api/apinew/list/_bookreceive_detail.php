<?php
require('../db.php');
$booksendId = $_GET['booksendid'];

$objsql = "SELECT fu.fac_bookcode,
    b.order_No,
    b.bsend_date,
    b.bsend_number,
    b.bsend_title,
    b.bsend_secret,
    b.bsend_rapid,
    b.bsend_note,
    d.booksenddetail_prefer,
    d.booksenddetail_filepath,
    d.booksenddetail_filename,
    d.status,
    r.breceive_number,
    r.breceive_date,
    tbr.tbookreceive_name,
    f.fac_name,
    fu.fac_name AS deptUser,
    CONCAT(m.userFname,' ',m.userLname) as userFullname
    FROM  tbbooksend_main b 
    LEFT JOIN tbbooksend_detail d ON b.bsend_id = d.bsend_id
    LEFT JOIN tbbookreceive_main r ON d.booksenddetail_id = r.booksenddetail_id
    LEFT JOIN tbtbookreceive tbr ON r.tbookreceive_id = tbr.tbookreceive_id
    LEFT JOIN tbfaculty f ON d.fac_receive = f.fac_code
    LEFT JOIN tbfaculty fu ON b.fac_code = fu.fac_code
    LEFT JOIN member_allow m ON b.userid = m.id
    WHERE d.booksenddetail_id = '$booksendId'";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    if($objdata['status'] == 'W'){
        $statusConfirm = "ยังไม่ยืนยันรับหนังสือ";
    }else if($objdata['status'] == 'C'){
        $statusConfirm = "ยืนยันรับหนังสือแล้ว";
    }
    if($objdata['bsend_note'] == ''){
        $bsend_note = '-';
    }
    $data[] = array(
        'booksendId'=>$booksendId,
        'orderNo'=>$objdata['order_No'],
        'booksendNumber'=>$objdata['fac_bookcode'].''.$objdata['bsend_number'],
        'bsendDate'=>$objdata['bsend_date'],
        'bsendTitle'=>$objdata['bsend_title'],
        'bsendSecret'=>$objdata['bsend_secret'],
        'bsendRapid'=>$objdata['bsend_rapid'],
        'bsendNote'=>$bsend_note,
        'breceiveNumber'=>$objdata['breceive_number'],
        'breceiveDate'=>$objdata['breceive_date'],
        'tbookreceiveName'=>$objdata['tbookreceive_name'],
        'preFer'=>$objdata['booksenddetail_prefer'],
        'filePath'=>$objdata['booksenddetail_filepath'],
        'fileName'=>$objdata['booksenddetail_filename'],
        'userFullname'=>$objdata['userFullname'],
        'facName'=>$objdata['fac_name'],
        'deptUser'=>$objdata['deptUser'],
        'statusConfirm'=>$statusConfirm,
        'Status'=>$objdata['status']
        
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
