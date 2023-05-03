<?php
require('../db.php');
$depart_id = $_GET['depart'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year']-543);
$userType = $_SESSION['userType']; //////////////////

//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว

if($depart_id == $departIduser || $userType == 'SA'){
    $condition = '';
    $roleRetire = 'Y';//Can retire to receive books.
}else{
    $condition = "AND e.secrets = '1'";
    $roleRetire = 'N';//cannot retire to receive books.
}

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    rc.receive_id,
    rc.status,
    rp.rapid_name,
    sc.secrets_name,
    d.depart_name,
    rc.pdf_path,
    rc.pdf_name,
    rc.pdf_name_retire,
    rc.pdf_path_retire,
    rc.receive_no,
    rc.depart_id_send,
    e.sender,
    e.sender_depart,
    t.edoc_type,
    rc.receive_type,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc_receive rc
    LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id
    LEFT JOIN department d ON rc.depart_id_send = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE rc.depart_id = '$depart_id' AND year(rc.receive_date) = '$Year'  AND rc.receive_status = '0'
    $condition
    ORDER BY rc.receive_date DESC, rc.receive_time DESC";
    
$objrs = mysqli_query($con, $objsql);
$i = 0;
while($objdata = mysqli_fetch_assoc($objrs)){ $i++;

    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);

    if($objdata['pdf_path_retire'] != ''){
        $pdf_path = $objdata['pdf_path_retire'];
    }else{
        $pdf_path = $objdata['pdf_path'];
    }
    
    $data[] = array(
        'No'=>$i,
        'receiveId'=>$objdata['receive_id'],
        'edocId'=>$objdata['edoc_id'],
        'edocType'=>$objdata['edoc_type'],
        'edocNo'=>$objdata['doc_no'],
        'receiveNo'=>$objdata['receive_no'],
        'edocDate'=>$edocDateNew,
        'filePath'=>$pdf_path,
        'fileName'=>$objdata['pdf_name'],
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departId_send'=>$objdata['depart_id_send'],
        'departName'=>$objdata['depart_name'],
        'senderDepart'=>$objdata['sender_depart'],
        'Sender'=>$objdata['sender'],
        'Status'=>$objdata['status'],
        'roleRetire'=>$roleRetire,
        'receiveType'=>$objdata['receive_type']
    );
}

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
