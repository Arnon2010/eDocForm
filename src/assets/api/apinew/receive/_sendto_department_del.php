<?php
require('../db.php');
$sentId = $_GET['sent_id'];
$edocId = $_GET['edoc_id'];
$departId = $_GET['depart_id'];
$departSendId = $_GET['depart_send_id'];


$objsql = "DELETE FROM edoc_sent
    WHERE sent_id = '$sentId'";
    
if($objrs = mysqli_query($con, $objsql)){
    $status = 'true';
    ## get sequence min ##
    /*
    $objsql_upd = "SELECT process_status, sequence FROM edoc_sent
        WHERE edoc_id = '$edocId'
        AND depart_id_send = '$departSendId'
        AND sequence_status = '1' AND process_status <> 'P'
        ORDER BY sequence ASC";
    */
    $objsql2 = "SELECT s1.sequence, s1.sent_id, s1.sent_status, s1.process_status
        FROM edoc_sent s1
          INNER JOIN
          (
            SELECT edoc_id, MIN(sequence) as MinSeqNO
            FROM edoc_sent
            WHERE edoc_id = '$edocId'
            AND depart_id_send = '$departSendId'
            AND sequence_status = '1' AND process_status = 'W' AND sent_status = '3'
            GROUP BY edoc_id
          ) s2
          ON s2.edoc_id = s1.edoc_id
        WHERE s2.MinSeqNO = s1.sequence";
        
    $objres2 = mysqli_query($con, $objsql2);
    $objdata2 = mysqli_fetch_array($objres2);
    
    ## update process status ##
    
    $objsql3 = "SELECT s1.sequence
        FROM edoc_sent s1
          INNER JOIN
          (
            SELECT edoc_id, MIN(sequence) as MinSeqNO
            FROM edoc_sent
            WHERE edoc_id = '$edocId'
            AND depart_id_send = '$departSendId'
            AND sequence_status = '1' AND process_status = 'P' AND sent_status = '3'
            GROUP BY edoc_id
          ) s2
          ON s2.edoc_id = s1.edoc_id
        WHERE s2.MinSeqNO = s1.sequence";
        
    $objres3 = mysqli_query($con, $objsql3);
    $objdata3 = mysqli_fetch_array($objres3);
    $numrow3 = mysqli_num_rows($objres3);
    
    if($numrow3 == 1)
        $processStatus = 'W'; //ลับก่อนหน้าได้เกษียนไปแล้ว กำหนดให้เป็นลำดับถัดไป
    else
        $processStatus = 'P'; //ลับก่อนหน้ายังไม่ได้เกษียน ต้องรอ
    
    $objsqlUp_process = "UPDATE edoc_sent SET process_status = '$processStatus'
        WHERE sent_id = '$objdata2[sent_id]'";
    mysqli_query($con, $objsqlUp_process);
    
    ## edoc sequence ##
    $objsql_seq = "DELETE FROM retire_sequence WHERE edoc_id = '$edocId' AND depart_id = '$departId' AND depart_send_id = '$departSendId'";
    mysqli_query($con, $objsql_seq);
    
    ## edoc track and log ##
    // status = 5 คือ หนังสือส่งต่อ
    $objsql_track = "DELETE FROM edoc_track WHERE edoc_id = '$edocId' AND depart_id = '$departId' AND status = '5'";
    mysqli_query($con, $objsql_track);
}
else{
    $status = 'false';
}
    $data[] = array(
        'Status'=>$status,
    );




header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
