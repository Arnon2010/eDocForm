<?php
require('../db.php');
require('../fn.php');
@$sentId = $_GET['sentid'];
   
$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.sender_depart,
    e.comment,
    s.sequence,
    s.pdf_path,
    s.pdf_name,
    s.user_id,
    s.depart_id_send,
    s.depart_id,
    s.sent_status,
    s.sequence_status,
    s.sent_comment,
    s.return_status,
    r.rapid_name,
    sc.secrets_name,
    da.depart_name AS departNameAgency,
    d.depart_name AS departName_send,
    dr.depart_name AS departName_receive,
    e.status,
    t.edoc_type_id,
    t.edoc_type_name,
    CONCAT(u.user_fname,' ',u.user_lname) as sender
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    LEFT JOIN department da ON e.depart_id = da.depart_id
    LEFT JOIN department d ON s.depart_id_send = d.depart_id
    LEFT JOIN department dr ON s.depart_id = dr.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON s.user_id = u.user_id
    WHERE s.sent_id = '$sentId'";


    
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){
    
    if($objdata['sent_status'] == '3'){ // หนังสือส่งต่อ
        $objsql2 = "SELECT distinct receive_no FROM edoc_receive WHERE edoc_id='$objdata[edoc_id]'";
        $objrs2 = mysqli_query($con, $objsql2);
        $objdata2 = mysqli_fetch_assoc($objrs2);
        $receiveNo = $objdata2['receive_no'];
    }else{
        $receiveNo = '';
    }
    
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);

    list($departIdSendReturn, $departNameSendReturn) = getDeptSendReturn($objdata['edoc_id'], $objdata['depart_id']);

    $data[] = array(
        'sentId'=>$sentId,
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'edocTypeId'=>$objdata['edoc_type_id'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'edocDate'=>$edocDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'senderDepart'=>$objdata['sender_depart'],
        'filePath'=>$objdata['pdf_path'],
        'fileName'=>$objdata['pdf_name'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departNameAgency'=>$objdata['departNameAgency'],
        'departName_send'=>$objdata['departName_send'],
        'departSendId'=>$objdata['depart_id_send'],
        'departReceiveId'=>$objdata['depart_id'],
        'departName_receive'=>$objdata['departName_receive'],
        'userId_send'=>$objdata['user_id'],
        'Sender'=>$objdata['sender'],
        'edocStatus'=>$objdata['status'],
        'receiveNo'=>$receiveNo,
        'Sequence'=>$objdata['sequence'],
        'sequenceStatus'=>$objdata['sequence_status'],
        'sentStatus'=>$objdata['sent_status'],
        'departIdSendReturn'=>$departIdSendReturn,
        'departNameSendReturn'=>$departNameSendReturn,
        'callbackComment'=>$objdata['sent_comment'],
        'returnStatus'=>$objdata['return_status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
