<?php
require('../db.php');
@$edocId = $_GET['edocid'];
//$User = $_GET[user];
$objsql = "SELECT DISTINCT e.edoc_id,
    e.doc_no,
    e.edoc_type_id,
    e.doc_date,
    e.sent_date,
    e.sent_time,
    e.headline,
    e.receiver,
    e.comment,
    r.rapid_id,
    r.rapid_name,
    s.secrets_id,
    s.secrets_name,
    d.depart_name,
    e.depart_id,
    e.status,
    e.sender,
    t.edoc_type,
    e.destroy_year,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname,
    deptu.depart_name as departOperator
    FROM  edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    LEFT JOIN department deptu ON u.depart_id = deptu.depart_id
    WHERE e.edoc_id = '$edocId'
    ORDER BY e.edoc_id DESC";
    
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
   
    $dateSent = explode("-",$objdata['sent_date']);
    $dateSentNew = $dateSent[2].'/'.$dateSent[1].'/'.($dateSent[0]);
    
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]);
    $data[] = array(
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'docTypeId'=>$objdata['edoc_type_id'],
        'type'=>$objdata['edoc_type'],
        'edocDate'=>$edocDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'sentDate'=>$dateSentNew,
        'sentTime'=>$objdata['sent_time'],
        'Comment'=>$objdata['comment'],
        'rapidId'=>$objdata['rapid_id'],
        'Rapid'=>$objdata['rapid_name'],
        'secretsId'=>$objdata['secrets_id'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'departId_edoc'=>$objdata['depart_id'],
        'Sender'=>$objdata['sender'],
        'edocStatus'=>$objdata['status'],
        'destroyYear'=>$objdata['destroy_year'],
        'departOperator'=>$objdata['departOperator']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
