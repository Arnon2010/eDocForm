<?php
require('../db.php');
$depart_id = $_GET['depart'];
    
$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.status,
    r.rapid_name,
    sc.secrets_name,
    d.depart_name,
    s.sent_status,
    s.pdf_path,
    s.pdf_name,
    u.user_fname,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id WHERE s.depart_id = '$depart_id'";
    
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
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'filePath'=>$objdata['pdf_path'],
        'fileName'=>$objdata['pdf_name'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'userSent'=>$objdata['user_fname'],
        'Status'=>$objdata['sent_status'],
        'edocStatus'=>$objdata['status'],
        'sql'=>$objsql
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
