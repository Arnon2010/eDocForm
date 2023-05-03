<?php
require('../db.php');

function get_status_sent_doc($edoc_id, $depart_id_send, $depart_id) {
    global $con;
    $row_status = mysqli_fetch_array(mysqli_query($con, "SELECT status_sent_doc FROM edoc_sent 
        WHERE depart_id_send = '$depart_id_send' 
        AND depart_id = '$depart_id' 
        AND edoc_id = '$edoc_id' 
        AND sent_status <> 'C'
    "));
    
    return $row_status['status_sent_doc'];
}

$depart_id = $_GET['depart'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year']-543);
//$userType = $_SESSION['userType']; //////////////////
$userType = $_GET['userType']; //////////////////


$qSearch = $_GET['qsearch'];

// page reload limit
$row = $_GET['row'];
$rowperpage = $_GET['rowperpage'];


//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว

if($depart_id == $departIduser || $userType == 'SA'){
    $condition = '';
    $roleRetire = 'Y';//Can retire to receive books.
}else{
    $condition = "AND e.secrets = '1'";
    $roleRetire = 'N';//cannot retire to receive books.
}

if($qSearch == 'notSearch'){
    $condition .= "";
} else {
    $arr_qsearch = explode("/",$qSearch);
    $date_search = ($arr_qsearch['2']-543).'-'.$arr_qsearch['1'].'-'.$arr_qsearch['0'];
    
    $condition .= " AND (rc.receive_no like '%$qSearch' 
        OR e.doc_no like '%$qSearch' 
        OR e.doc_date = '$date_search' 
        OR e.headline like '%$qSearch%' 
        OR e.receiver like '%$qSearch%' 
        OR e.comment like '%$qSearch%' 
        OR e.sender like '%$qSearch%'
        OR e.sender_depart like '%$qSearch%'
        OR e.comment like '%$qSearch%'
        OR rp.rapid_name like '%$qSearch%'
        OR d.depart_name like '%$qSearch%' 
        OR t.edoc_type_name like '%$qSearch%'
    )";
}

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    e.sender,
    e.sender_depart,
    rp.rapid_name,
    sc.secrets_name,
    e.depart_id,
    d.depart_name AS edoc_dept_name,
    rc.receive_id,
    rc.status,
    rc.pdf_path,
    rc.pdf_name,
    rc.pdf_name_retire,
    rc.pdf_path_retire,
    rc.receive_no,
    rc.depart_id_send,
    rc.depart_id AS depart_id_receive,
    t.edoc_type,
    rc.receive_type,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM edoc_receive rc
    LEFT JOIN edoc e ON rc.edoc_id = e.edoc_id 
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE rc.depart_id = '$depart_id' AND year(rc.receive_date) = '$Year'  
    AND rc.receive_status = '0'
    $condition 
    ORDER BY rc.receive_date DESC, rc.receive_time DESC 
    limit $row, $rowperpage";
    
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

    // แสดงสถานะส่งหนังสือแบบปกติ หรือส่งฉบับจริง
    $status_sent_doc = get_status_sent_doc($objdata['edoc_id'], $objdata['depart_id_send'], $objdata['depart_id_receive']);

    
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
        'departId_doc'=>$objdata['depart_id'],
        'departId_send'=>$objdata['depart_id_send'],
        'edocDepartName'=>$objdata['edoc_dept_name'],
        'senderDepart'=>$objdata['sender_depart'],
        'Sender'=>$objdata['sender'],
        'Status'=>$objdata['status'],
        'roleRetire'=>$roleRetire,
        'receiveType'=>$objdata['receive_type'],
        'status_sent_doc'=> $status_sent_doc
    );
}

//$data[] = array('qSearch'=>$condition);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

if(count($data) > 0)
    print json_encode($data);
    
exit;
