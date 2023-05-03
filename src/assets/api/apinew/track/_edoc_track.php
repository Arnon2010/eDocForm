<?php
require('../db.php');
$edocId = $_GET['edocid'];

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.sender_depart,
    r.rapid_name,
    s.secrets_name,
    d.depart_name,
    e.status,
    e.sender,
    t.edoc_type_name,
    t.edoc_type,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM  edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE e.edoc_id = '$edocId'";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;
      
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    
    $data[] = array(
        'No'=>$i,
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'edocDate'=>$edocDateNew,
        'docType'=>$objdata['edoc_type'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'senderDepart'=>$objdata['sender_depart'],
        'Sender'=>$objdata['sender'],
        'edocStatus'=>$objdata['status']
    );
}

//$data[] = array('sql'=>$departIduser);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
