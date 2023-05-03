<?php
/*
# offer_status
# W = รอพิจารณา
# C = อนุมัติแล้ว
*/
require('../db.php');
$orderNo = $_GET[order_no];
$facCode = $_GET[faccode];
$writeTime = $_GET[wtime];//เวลาเขียนเลขและวันที่ส่งหนังสือ
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
    WHERE s.order_No = '$orderNo'
    AND s.faccode = '$facCode'
    AND s.status = '4'
    AND o.status = 'C'";
    
$objrs = mysqli_query($con, $objsql);
while($objdata = mysqli_fetch_assoc($objrs)){
    
    $objsql2 = "SELECT isunumber_filepath FROM tbissuenumber_temp
    WHERE offerdoc_id = '$objdata[offerdoc_id]'";  
    $objresult2 = mysqli_query($con, $objsql2);
    $numRow = mysqli_num_rows($objresult2);
    $data_numsend = mysqli_fetch_array($objresult2);
    if($numRow == 1){
        $statusWritten = 1; //เขียนเลขหนังสือส่งและวันที่แล้ว
        $statusWrite = 0;//ยังไม่เขียนเลข
    }else{
        $statusWritten = 0;
        $statusWrite = 1;
    }

    $data[] = array(
        'offerId'=>$objdata['offerdoc_id'],
        'orderNo'=>$objdata['order_No'],
        'Title'=>$objdata['submitmain_title'],
        'Prefer'=>$objdata['offerdoc_prefer'],
        'fileName'=>$objdata['offerdoc_filename'],
        'filePath_Sign'=>$objdata['offersign_filepath'],
        'filePath_Numsend'=>$data_numsend['isunumber_filepath'],
        'facId'=>$objdata['fac_id'],
        'facCode'=>$objdata['fac_code'],
        'facName'=>$objdata['fac_name'],
        'fullName'=>$objdata[userFname].' '.$objdata[userLname],
        'nameSupervisors'=>$objdata[nameSupervisors],
        'statusWrite'=>$statusWrite,
        'statusWritten'=>$statusWritten,
        'status'=>$numRow
    );
}

//$data[] = array('sqltest'=>$objsql);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
