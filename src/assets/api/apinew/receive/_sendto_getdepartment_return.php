<?php
require('../db.php');
require('../fn.php');
@$edocId = $_GET['edocid'];
@$departId = $_GET['departid_sendto'];//หน่วยงานส่งหนังสือ

/*
*/
$objsql = "SELECT s.sent_id,
        s.sequence,
        s.depart_id_send,
        d.depart_name,
        d.depart_id,
        s.pdf_name,
        s.pdf_path,
        s.sequence_status,
        s.sent_status,
        s.sent_comment
        FROM edoc_sent s
        LEFT JOIN department d ON s.depart_id = d.depart_id
        WHERE s.edoc_id = '$edocId' AND depart_id_send = '$departId'
        AND s.sent_status = '3'
        ORDER BY s.sent_id ASC";

$objrs = mysqli_query($con, $objsql);
$No = $objdata['sequence'];
$No = 0;
while($objdata = mysqli_fetch_assoc($objrs) ){ $No++;

    $departName = getDepartmentClass($objdata['depart_id']);
    
    $data[] = array(
        'No'=>$No,
        'sentId'=>$objdata['sent_id'],
        'deptId'=>$objdata['depart_id'],
        'deptName'=>$departName,
        'deptIdSend'=>$objdata['depart_id_send'],
        'fileName'=>$objdata['pdf_name'],
        'filePath'=>$objdata['pdf_path'],
        'sequenceStatus'=>$objdata['sequence_status'],
        'sentStatus'=>$objdata['sent_status'],
        'sentComment'=>$objdata['sent_comment']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>