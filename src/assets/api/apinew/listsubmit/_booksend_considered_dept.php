<?php
/*
# offer_status
# W = รอพิจารณา
# C = อนุมัติแล้ว
*/
require('../db.php');
$offerdocId = $_GET[offer];
//$User = $_GET[user];
$objsql = "SELECT
    s.order_No,
    s.submitmain_title,
    d.offerdoc_id,
    d.offerdoc_prefer,
    d.offerdoc_filename,
    sign.offersign_filepath,
    m.userFname, m.userLname, f.fac_name, f.fac_code, f.fac_id,
    CONCAT(name,' ',lastname) as nameSupervisors
    FROM tbsubmit_main s
    LEFT JOIN tbofferbooksend_main o ON s.order_No = o.order_No
    LEFT JOIN tbofferbooksend_doc d ON o.offermain_id = d.offermain_id
    LEFT JOIN tbofferbooksend_sign sign ON d.offerdoc_id = sign.offerdoc_id
    LEFT JOIN member_allow m ON o.user = m.id
    LEFT JOIN member ON o.offermain_epassport = member.e_passport
    LEFT JOIN tbfaculty f ON d.fac_receive = f.fac_code
    WHERE d.offerdoc_id = '$offerdocId' AND s.status = '4' AND o.status = 'C'";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
  
    $data[] = array(
        'offerId'=>$objdata['offerdoc_id'],
        'orderNo'=>$objdata['order_No'],
        'Title'=>$objdata['submitmain_title'],
        'Prefer'=>$objdata['offerdoc_prefer'],
        'fileName'=>$objdata['offerdoc_filename'],
        'filePath'=>$objdata['offersign_filepath'],
        'facId'=>$objdata['fac_id'],
        'facCode'=>$objdata['fac_code'],
        'facName'=>$objdata['fac_name'],
        'fullName'=>$objdata[userFname].' '.$objdata[userLname],
        'nameSupervisors'=>$objdata[nameSupervisors]
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
