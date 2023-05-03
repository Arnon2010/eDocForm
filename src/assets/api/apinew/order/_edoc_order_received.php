<?php
require('../db.php');
@$edocId = $_GET['edocid'];
@$departId = $_GET['departid'];
   
$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    r.receive_no,
    r.receive_date,
    r.receive_time,
    e.headline,
    e.receiver,
    e.comment,
    r.pdf_path,
    r.pdf_name,
    rp.rapid_name,
    sc.secrets_name,
    d.depart_name,
    e.status,
    t.edoc_type_id,
    t.edoc_type_name,
    u.user_fname,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname,
    CONCAT(ur.user_fname,' ',ur.user_lname) as userReceive
    FROM edoc_receive r
    LEFT JOIN edoc_user ur ON r.user_id = ur.user_id
    LEFT JOIN edoc e ON r.edoc_id = e.edoc_id
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE r.depart_id = '$departId' AND r.edoc_id = '$edocId'";


    
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
    
    $edocDate = explode("-",$objdata['receive_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    
    $receiveDate = explode("-",$objdata['doc_date']);
    $receiveDateNew = $receiveDate[2].'/'.$receiveDate[1].'/'.($receiveDate[0]+543);
    
    $data[] = array(
        'edocId'=>$objdata['edoc_id'],
        'edocNo'=>$objdata['doc_no'],
        'edocTypeId'=>$objdata['edoc_type_id'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'edocDate'=>$edocDateNew,
        'receiveNo'=>$objdata['receive_no'],
        'receiveTime'=>$objdata['receive_time'],
        'receiveDate'=>$receiveDateNew,
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'filePath'=>$objdata['pdf_path'],
        'fileName'=>$objdata['pdf_name'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'Sender'=>$objdata['userFullname'],
        'userReceive'=>$objdata['userReceive'],
        'edocStatus'=>$objdata['status']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
