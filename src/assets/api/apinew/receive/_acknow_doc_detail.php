<?php

require('../db.php');
@$mainId = $_GET['main_id'];
@$receiveNo = $_GET['receive_no'];

// max move id
$objsql_max = "SELECT MAX(move_id) as max_move_id 
    FROM sign_move 
    WHERE main_id = '$mainId' AND move_status = '1'";
$objrs_max = mysqli_query($con, $objsql_max);
$data_max = mysqli_fetch_array($objrs_max);
$move_id_max = $data_max['max_move_id'];


$objsql_act = "SELECT activity
    FROM sign_move
    WHERE main_id = '$mainId' 
    AND move_id = '$move_id_max'";
$objrs_act = mysqli_query($con, $objsql_act);
$data_act = mysqli_fetch_array($objrs_act);

$Activity = $data_act['activity'];

$objsql = "SELECT e.edoc_id,
    e.doc_no,
    e.doc_date,
    r.receive_no,
    r.receive_date,
    r.receive_time,
    r.depart_id,
    e.headline,
    e.receiver,
    e.sender_depart,
    e.comment,
    r.pdf_path,
    r.pdf_name,
    rp.rapid_name,
    sc.secrets_name,
    dr.depart_name as deptreceive,
    ds.depart_name as deptsend,
    e.status,
    t.edoc_type_id,
    t.edoc_type_name,
    e.sender,
    r.receive_type,
    CONCAT(u.user_fname,' ',u.user_lname) as userReceive,
    m.main_type,
    m.main_status,
    m.main_id
    FROM sign_main m
    LEFT JOIN edoc_receive r ON m.edoc_id = r.edoc_id
    LEFT JOIN edoc e ON r.edoc_id = e.edoc_id
    LEFT JOIN department ds ON r.depart_id_send = ds.depart_id
    LEFT JOIN department dr ON r.depart_id = dr.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid rp ON e.rapid = rp.rapid_id
    LEFT JOIN secrets sc ON e.secrets = sc.secrets_id
    LEFT JOIN edoc_user u ON r.user_id = u.user_id
    WHERE m.main_id = '$mainId' 
    AND r.receive_no = '$receiveNo'";

$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
    
    $edocDate = explode("-",$objdata['doc_date']);
    $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    
    $receiveDate = explode("-",$objdata['receive_date']);
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
        'senderDepart'=>$objdata['sender_depart'],
        'filePath'=>$objdata['pdf_path'],
        'fileName'=>$objdata['pdf_name'],
        'Comment'=>$objdata['comment'],
        'Rapid'=>$objdata['rapid_name'],
        'Secrets'=>$objdata['secrets_name'],
        'deptReceiveId'=>$objdata['depart_id'],
        'deptReceive'=>$objdata['deptreceive'],
        'deptSend'=>$objdata['deptsend'],
        'Sender'=>$objdata['sender'],
        'userReceive'=>$objdata['userReceive'],
        'edocStatus'=>$objdata['status'],
        'receiveType'=>$objdata['receive_type'],
        'mainId'=>$objdata['main_id'],
        'mainType'=>$objdata['main_type'],
        'mainStatus'=>$objdata['main_status']
    );
}


$data_activity[] = array('Activity'=>$Activity);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data, "data_activity"=>$data_activity));
