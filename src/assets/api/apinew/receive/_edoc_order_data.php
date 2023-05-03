<?php
require('../db.php');
$userType = $_GET['utype'];
$depart_id = $_GET['departid'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year']-543);

$roleRegister = 'Y';
$condition = '';
//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว
/*
if($depart_id == $departIduser || $userType == 'SA'){
    $condition = '';
    $roleRegister = 'Y';//Can register to receive books.
}else{
    $condition = "AND e.secrets = '1'";
    $roleRegister = 'N';//cannot register to receive books.
}*/

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.status,
    e.secrets,
    r.rapid_name,
    sc.secrets_name,
    d.depart_name,
    s.sent_id,
    s.sent_status,
    s.pdf_path,
    s.pdf_name,
    s.date_sendto,
    s.time_sendto,
    s.status_sent_doc,
    e.sender,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc_sent s
    LEFT JOIN edoc e ON s.edoc_id = e.edoc_id
    LEFT JOIN department d ON s.depart_id_send = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON s.user_id = u.user_id 
    
    WHERE s.depart_id = '$departIduser' AND s.sent_status = '1' $condition
    ORDER BY s.date_sendto DESC, s.time_sendto DESC";

$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;

    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    
    $sendDate = explode("-",$objdata['date_sendto']);
    $sendDateNew = $sendDate[2].'/'.$sendDate[1].'/'.($sendDate[0]+543);
    
    $data[] = array(
        'No'=>$i,
        'sentId'=>$objdata['sent_id'],
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
        'Sender'=>$objdata['sender'],
        'userSender'=>$objdata['userFullname'],
        'sendDate'=>$sendDateNew,
        'sendTime'=>$objdata['time_sendto'],
        'Status'=>$objdata['sent_status'],
        'edocStatus'=>$objdata['status'],
        'statusSentDoc'=>$objdata['status_sent_doc'],
        'roleRegister'=>$roleRegister
    );
}


header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
