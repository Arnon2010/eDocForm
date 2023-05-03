<?php
require('../db.php');
require('../fn.php');

@$edocId = $_GET['edocid'];
@$departId = $_GET['departid'];
@$receiveNo = $_GET['receive_no'];
   
$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    r.receive_id,
    r.receive_no,
    r.receive_date,
    r.receive_time,
    r.depart_id,
    r.depart_id_send,
    r.receive_status,
    e.headline,
    e.receiver,
    e.sender_depart,
    e.comment,
    r.pdf_path,
    r.pdf_name,
    r.pdf_path_retire,
    r.pdf_name_retire,
    rp.rapid_name,
    sc.secrets_name,
    dr.depart_name as deptreceive,
    ds.depart_name as deptsend,
    e.status,
    t.edoc_type,
    t.edoc_type_id,
    t.edoc_type_name,
    e.sender,
    CONCAT(u.user_fname,' ',u.user_lname) as userReceive
    FROM edoc_receive r
    LEFT JOIN edoc e ON r.edoc_id = e.edoc_id
    LEFT JOIN department ds ON r.depart_id_send = ds.depart_id
    LEFT JOIN department dr ON r.depart_id = dr.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON r.user_id = u.user_id
    WHERE r.depart_id = '$departId' 
    AND r.receive_no = '$receiveNo' 
    AND r.edoc_id = '$edocId'";
    
$objrs = mysqli_query($con, $objsql);

//while($objdata = mysqli_fetch_assoc($objrs)){ 
    $objdata = mysqli_fetch_assoc($objrs);
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    
    $receiveDate = explode("-",$objdata['receive_date']);
    $receiveDateNew = $receiveDate[2].'/'.$receiveDate[1].'/'.($receiveDate[0]+543);

    $sentStatus = getStatusReceivedReturn($objdata['edoc_id'], $objdata['depart_id']);
    
    $data[] = array(
        'receiveId'=>$objdata['receive_id'],
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'edocType'=>$objdata['edoc_type'],
        'edocTypeId'=>$objdata['edoc_type_id'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'edocDate'=>$edocDateNew,
        'receiveNo'=>$objdata['receive_no'],
        'receiveTime'=>$objdata['receive_time'],
        'receiveDate'=>$receiveDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'senderDepart'=>$objdata['sender_depart'],
        'filePath'=>$objdata['pdf_path'],
        'fileName'=>$objdata['pdf_name'],
        'filePathRetire'=>$objdata['pdf_path_retire'],
        'fileNameRetire'=>$objdata['pdf_name_retire'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'deptReceiveId'=>$objdata['depart_id'],
        'deptReceive'=>$objdata['deptreceive'],
        'deptSendId'=>$objdata['depart_id_send'],
        'deptSend'=>$objdata['deptsend'],
        'Sender'=>$objdata['sender'],
        'userReceive'=>$objdata['userReceive'],
        'edocStatus'=>$objdata['status'],
        'receiveStatus'=>$objdata['receive_status'],
        'sentStatus'=>$sentStatus
    );
//}

$data[] = array('resp'=>'');

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
