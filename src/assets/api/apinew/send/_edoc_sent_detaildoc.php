<?php
require('../db.php');
@$edocId = $_GET['edocid'];
//$User = $_GET[user];
$objsql = "SELECT DISTINCT e.edoc_id,
    e.doc_no,
    t.edoc_type,
    t.edoc_type_name,
    e.doc_date,
    e.sent_date,
    e.sent_time,
    e.headline,
    e.receiver,
    e.comment,
    e.sender_depart,
    r.rapid_name,
    s.secrets_name,
    d.depart_name,
    e.status,
    u.user_fname,
    e.sender,
    e.depart_id,
    e.destroy_year,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM  edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE e.edoc_id = '$edocId'
    ORDER BY e.edoc_id DESC";
    
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
   
    $dateSent = explode("-",$objdata['sent_date']);
    $dateSentNew = $dateSent[2].'/'.$dateSent[1].'/'.($dateSent[0]+543);
    
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    $data[] = array(
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'docType'=>$objdata['edoc_type'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'edocDate'=>$edocDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'sentDate'=>$dateSentNew,
        'sentTime'=>$objdata['sent_time'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'departId_edoc'=>$objdata['depart_id'],
        'Sender'=>$objdata['sender'],
        'senderDepart'=>$objdata['sender_depart'],
        'destroyYear'=>$objdata['destroy_year'],
        'edocStatus'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
