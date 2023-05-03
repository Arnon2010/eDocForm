<?php
require('../db.php');
$orderNo = $_GET[order_no];
$facCode = $_GET[faccode];
//$User = $_GET[user];
/*
$objsql = "SELECT s.*, 
    s.order_No,
    s.submitmain_title,
    s.status,
    o.offer_date,
    o.offer_prefer,
    o.offer_filepath,
    o.offer_filename,
    o.offer_datemodified,
    m.userFname, m.userLname, f.fac_name, f.fac_code, f.fac_id, CONCAT(name,' ',lastname) as nameSupervisors
    FROM tbsubmit_main s
    LEFT JOIN tbofferbooksend o ON s.order_No = o.order_No
    LEFT JOIN member_allow m ON o.user = m.id
    LEFT JOIN member ON o.offer_epassport = member.e_passport
    LEFT JOIN tbfaculty f ON o.fac_receive = f.fac_code
    WHERE s.order_No = '$orderNo'";
*/
$objsql = "SELECT om.*, 
    om.order_No,
    om.offermain_title,
    om.status,
    om.offermain_date,
    d.offerdoc_prefer,
    d.offerdoc_filepath,
    d.offerdoc_filename,
    om.datemodified,
    m.userFname, m.userLname, f.fac_name, f.fac_code, f.fac_id, CONCAT(name,' ',lastname) as nameSupervisors
    FROM tbofferbooksend_main om
    LEFT JOIN tbofferbooksend_doc d ON om.offermain_id = d.offermain_id
    LEFT JOIN member_allow m ON om.user = m.id
    LEFT JOIN member ON om.offermain_epassport = member.e_passport
    LEFT JOIN tbfaculty f ON d.fac_receive = f.fac_code
    WHERE om.order_No = '$orderNo' AND om.faccode = '$facCode'";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    if($objdata['status'] == '1'){
        $status = "ยื่นเอกสาร (รอตรวจสอบ)";
    }
    $data[] = array(
        'orderNo'=>$objdata['order_No'],
        'dateOffer'=>$objdata['offermain_date'],
        'Title'=>$objdata['offermain_title'],
        'Prefer'=>$objdata['offerdoc_prefer'],
        'filePath'=>$objdata['offerdoc_filepath'],
        'fileName'=>$objdata['offerdoc_filename'],
        'facId'=>$objdata['fac_id'],
        'facCode'=>$objdata['fac_code'],
        'facName'=>$objdata['fac_name'],
        'dateModified'=>$objdata['datemodified'],
        'fullName'=>$objdata[userFname].' '.$objdata[userLname],
        'nameSupervisors'=>$objdata[nameSupervisors],
        'status'=>$status
    );
}

//$data[] = array('sqltest'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
