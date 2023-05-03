<?php
require('../db.php');
require('../fn.php');
@$edocId = $_GET['edocid'];

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
        s.sent_comment,
        s.status_sent_doc
        FROM edoc_sent s
        LEFT JOIN department d ON s.depart_id = d.depart_id
        WHERE s.edoc_id = '$edocId'
        AND s.sent_status in ('3','4','R')
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
        'sentComment'=>$objdata['sent_comment'],
        'statusSentDoc'=>$objdata['status_sent_doc']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
?>