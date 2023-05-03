<?php
require('../db.php');
$orderNo = $_GET[order_no];
$mainId = $_GET[mainid];
$facCode = $_GET[faccode];
$tempId = $_GET[tempid];

//$User = $_GET[user];
$objsql = "SELECT s.order_No,
    s.submitmain_date,
    s.submitmain_title,
    s.datemodified,
    s.status,
    d.submitdoc_id,
    d.submitdoc_prefer,
    d.submitdoc_filepath,
    d.submitdoc_filename,
    m.userFname, m.userLname, f.fac_name, f.fac_code, f.fac_id
    FROM tbsubmit_main s
    LEFT JOIN tbsubmit_doc d ON s.submitmain_id = d.submitmain_id
    LEFT JOIN member_allow m ON s.user = m.id 
    LEFT JOIN tbfaculty f ON d.fac_receive = f.fac_code
    WHERE s.submitmain_id = '$mainId' AND s.order_No = '$orderNo' AND s.faccode = '$facCode'";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
    $objsqlchk = "SELECT tempoffer_id FROM tbofferbooksend_temp 
    WHERE submitdoc_id='$objdata[submitdoc_id]'
    AND tempoffer_id = '$tempId'";
    $objrschk = mysqli_query($con, $objsqlchk);
    $numRow = mysqli_num_rows($objrschk);
    if($numRow == '1'){
        $statusOffertemp = 1;
        $statusOfferadd = 0;
    }else{
         $statusOffertemp = 0;
         $statusOfferadd = 1;
    }
    $data[] = array(
        'No'=>$i,
        'submitdocId'=>$objdata['submitdoc_id'],
        'orderNo'=>$objdata['order_No'],
        'Date'=>$objdata['submitmain_date'],
        'Title'=>$objdata['submitmain_title'],
        'Prefer'=>$objdata['submitdoc_prefer'],
        'filePath'=>$objdata['submitdoc_filepath'],
        'fileName'=>$objdata['submitdoc_filename'],
        'facId'=>$objdata['fac_id'],
        'facCode'=>$objdata['fac_code'],
        'facName'=>$objdata['fac_name'],
        'dateModified'=>$objdata['datemodified'],
        'fullName'=>$objdata[userFname].' '.$objdata[userLname],
        'status'=>$status,
        'statusOffertemp'=>$statusOffertemp,
        'statusOfferadd'=>$statusOfferadd,
        'sql'=>$objsqlchk
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
