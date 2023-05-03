<?php
require('../db.php');
@$edocId = $_GET['edocid'];
@$departId = $_GET['departid'];
@$departId_send = $_GET['departid_send'];
   
$objsql = "SELECT sent_id, sent_status, depart_id_send
    FROM edoc_sent 
    WHERE depart_id = '$departId'
    AND edoc_id = '$edocId'
    AND depart_id_send = '$departId_send'";

$objrs = mysqli_query($con, $objsql);
$objdata = mysqli_fetch_assoc($objrs);

$objsql2 = "SELECT s1.sent_id, s1.sent_status, s1.process_status
    FROM edoc_sent s1
    INNER JOIN
    (
        SELECT edoc_id, MIN(sequence) as MinSeqNO
        FROM edoc_sent
        WHERE depart_id <> '$departId'
        AND edoc_id = '$edocId'
        AND depart_id_send = '$departId_send'
        AND sequence_status = '1'
        GROUP BY edoc_id
    ) s2
    ON s2.edoc_id = s1.edoc_id
    WHERE s2.MinSeqNO = s1.sequence";
                
    $objres2 = mysqli_query($con, $objsql2);
    $objdata2 = mysqli_fetch_array($objres2);
    
$data[] = array(
        'sentStatus'=>$objdata['sent_status'],
        'deptIdSetSeq'=>$objdata['depart_id_send'],
        'sentIdNext'=>$objdata2['sent_id'],
        'sentStatusNext'=>$objdata2['sent_status'],
        'processStatusNext'=>$objdata2['process_status'],
);

$data[] = array('resp'=>$objsql2);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
