<?php
require('../db.php');
$request = json_decode(file_get_contents("php://input"));

$facId = $request->facId;//หน่วยงานที่ส่ง
$facCode = $request->facCode;//รหัสหน่วยงาน
$bsendDate = $request->bsendDate;
$Secret = $request->Secret;
$Rapid = $request->Rapid;
$tbooksendId = $request->tbooksendId;
$tbooksendNew = $request->tbooksendNew;
$bsendTo = $request->bsendTo;
$bsendPractice = $request->bsendPractice;
$bsendSubject = $request->bsendSubject;
$userId = $request->userId;
$bsendTime = date('H:i:s');

$arrbsendDate = explode("/",$bsendDate);
$bsendDate = ($arrbsendDate[2]-543).'-'.$arrbsendDate[1].'-'.$arrbsendDate[0];

$stamp = strtotime(date('Y-m-d H:i:s'));
$bsend_code = $facCode.'T'.$stamp;
$objsql = "INSERT INTO tbbooksend( bsend_id,
    bsend_code,
    bsend_date, 
    bsend_time, 
    bsend_secret, 
    bsend_rapid, 
    tbooksend_id, 
    bsend_to, 
    bsend_subject,
    userId,
    bsend_practice,
    bsend_facid,
    bsend_step,
    status
) VALUES(null,
    '$bsend_code',
    '$bsendDate',
    '$bsendTime',
    '$Secret',
    '$Rapid',
    '$tbooksendId',
    '$bsendTo',
    '$bsendSubject',
    '$userId',
    '$bsendPractice',
    '$facId',
    '1',
    '1'
)";
  
if(mysqli_query($con, $objsql)){

    $objsql = "SELECT * FROM tbnsend WHERE status = '1' AND fac_id = '$facId' AND tbooksend_id = '$tbooksendId'";
    $objrs = mysqli_query($con, $objsql);
    $objdata = mysqli_fetch_assoc($objrs);
    $row = mysqli_num_rows($objrs);
    if($row == 0){
        $noSend = 1;
        //insert to table...
        $sqlnSend = "INSERT INTO tbnsend (nsend_id, fac_id, tbooksend_id, nsend_no, status)
            VALUES(null, '$facId', '$tbooksendId', '$noSend', '1')";
        mysqli_query($con, $sqlnSend);
        
    }else{
        $noSend = $objdata[nsend_no]+1;
        $sqlnSend = "UPDATE tbnsend SET nsend_no = '$noSend' WHERE fac_id = '$facId' AND tbbooksend_id = '$tbooksendId'";
        mysqli_query($con, $sqlnSend);
    }
    $status = "true";
}else{
    $status = "false";
} 
$data[] = array(
    'status'=>$status, 
    'noSend'=>$noSend,
    'bsendTime'=>$bsendTime
);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);
print json_encode(array("data"=>$data));

