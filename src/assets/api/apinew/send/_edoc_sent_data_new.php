<?php
session_start();
require('../db.php');
$depart_id = $_GET['depart'];
$departIduser = $_GET['depart_iduser'];
$Year = ($_GET['year'] - 543);
$userType = $_GET['usertype'];
$edocType = $_GET['doctype'];

$row = $_GET['row'];
$rowperpage = $_GET['rowperpage'];

//กรณีชั้นความที่มากปกติ เจ้าของเรื่องหรือหน่วยงานที่เกี่ยวข้องมีสิทธิมองเห็นแค่คนเดียว  

$condition = '';
if($depart_id == $departIduser || $userType == 'SA'){
    $condition = '';
    $roleSend = 'Y';//Can edit to send books.
}else{
    $condition = " AND e.secrets = '1'";
    $roleSend = 'N';//cannot edit to send books.
}

if($edocType == 'all' || $edocType == 'undefined')
    $condition .= "";
else{
    $condition .= " AND e.edoc_type_id = '$edocType'";
}
    

if($departIduser == '35')  //หน่ายงานมหาลัย
    $condition .= "OR e.edoc_id IN (
        SELECT edoc_id FROM edoc_univ_no
    )";

$condition .= " AND e.doc_no <> ''";

// ค้นหา
$qSearch = $_GET['qsearch'];

if($qSearch == 'notSearch'){
    $condition .= "";
} else {
    $arr_qsearch = explode("/",$qSearch);
    $date_search = ($arr_qsearch['2']-543).'-'.$arr_qsearch['1'].'-'.$arr_qsearch['0'];
    
    $condition .= " AND (e.doc_no like '%$qSearch' 
        OR e.doc_date = '$date_search' 
        OR e.headline like '%$qSearch%' 
        OR e.receiver like '%$qSearch%' 
        OR e.comment like '%$qSearch%' 
        OR e.sender like '%$qSearch%'
        OR e.sender_depart like '%$qSearch%'
        OR e.comment like '%$qSearch%'
        OR r.rapid_name like '%$qSearch%'
        OR s.secrets_name like '%$qSearch%'
        OR d.depart_name like '%$qSearch%' 
        OR t.edoc_type_name like '%$qSearch%' 
        OR u.user_fname like '%$qSearch%' 
        OR u.user_lname like '%$qSearch%'
    )";
}

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    e.headline,
    e.receiver,
    e.comment,
    r.rapid_name,
    s.secrets_name,
    d.depart_name,
    e.status,
    e.sender,
    t.edoc_type_name,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname
    FROM  edoc e
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE e.depart_id = '$depart_id' AND year(e.sent_date) = '$Year'
    AND e.edoc_id NOT IN (
        SELECT edoc_id FROM edoc_receive WHERE receive_type = '2'
    )
    $condition
    ORDER BY e.edoc_id DESC 
    limit $row, $rowperpage";
    
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
        'edocTypeName'=>$objdata['edoc_type_name'],
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'departName'=>$objdata['depart_name'],
        'Sender'=>$objdata['sender'],
        'edocStatus'=>$objdata['status'],
        'roleSend'=>$roleSend
    );
}

//$data[] = array('sql'=>$objsql);

@header("Access-Control-Allow-Origin: *");
@header("content-type:text/javascript;charset=utf-8");
@header("Content-Type: application/json; charset=utf-8", true, 200);

if(count($data) > 0)
    @print json_encode($data);
    
exit;
