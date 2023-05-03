<?php
require('../db.php');
$orderNo = $_GET[order_no];
//$User = $_GET[user];
$objsql = "SELECT s.*, m.userFname, m.userLname, f.fac_name, f.fac_code, f.fac_id
    FROM tbsubmitdoc s
    LEFT JOIN member_allow m ON s.user = m.e_passport
    LEFT JOIN tbfaculty f ON s.faccode_receive = f.fac_code
    WHERE s.submit_order = '$orderNo'
    ORDER BY s.date_modified DESC";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    if($objdata['submit_status'] == 'W'){
        $status = "ยื่นเอกสาร (รอตรวจสอบ)";
    }
    $data[] = array(
        'orderNo'=>$objdata['submit_order'],
        'No'=>$objdata['submit_no'],
        'Date'=>$objdata['submit_date'],
        'Title'=>$objdata['submit_title'],
        'Prefer'=>$objdata['submit_prefer'],
        'filePath'=>$objdata['submit_filepath'],
        'fileName'=>$objdata['submit_filename'],
        'facId'=>$objdata['fac_id'],
        'facCode'=>$objdata['fac_code'],
        'facName'=>$objdata['fac_name'],
        'dateModified'=>$objdata['date_modified'],
        'fullName'=>$objdata[userFname].' '.$objdata[userLname],
        'status'=>$status
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
