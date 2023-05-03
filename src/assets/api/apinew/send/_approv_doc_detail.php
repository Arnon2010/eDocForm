<?php
require('../db.php');
@$mainId = $_GET['main_id'];

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

//$User = $_GET[user];
$objsql = "SELECT DISTINCT e.edoc_id, m.main_id,
    e.doc_no,
    t.edoc_type_id,
    t.edoc_type,
    t.edoc_type_name,
    e.doc_date,
    e.sent_date,
    e.sent_time,
    e.headline,
    e.receiver,
    e.comment,
    e.sender_depart,
    r.rapid_id,
    r.rapid_name,
    s.secrets_id,
    s.secrets_name,
    d.depart_name,
    e.status,
    u.user_fname,
    e.depart_id,
    e.destroy_year,
    CONCAT(u.user_fname,' ',u.user_lname) as userFullname,
    m.main_type,
    m.main_status
    FROM  sign_main m 
    LEFT JOIN edoc e ON m.edoc_id = e.edoc_id
    LEFT JOIN department d ON e.depart_id = d.depart_id
    LEFT JOIN edoc_type t ON e.edoc_type_id = t.edoc_type_id
    LEFT JOIN rapid r ON e.rapid = r.rapid_id
    LEFT JOIN secrets s ON e.secrets = s.secrets_id
    LEFT JOIN edoc_user u ON e.user_id = u.user_id
    WHERE m.main_id = '$mainId'
    ORDER BY e.edoc_id DESC";
    
$objrs = mysqli_query($con, $objsql);

while($objdata = mysqli_fetch_assoc($objrs)){ 
    
    // $dateSent = explode("-",$objdata['sent_date']);
    // $dateSentNew = $dateSent[2].'/'.$dateSent[1].'/'.($dateSent[0]+543);
    if($objdata['doc_date'] == '' || $objdata['doc_date'] == '0000-00-00') {
        $edocDateNew = date('d').'/'.date('m').'/'.(date('Y') + 543);
    } else {
        $edocDate = explode("-",$objdata['doc_date']);
        $edocDateNew = $edocDate[2].'/'.$edocDate[1].'/'.($edocDate[0]+543);
    }
    

    $str_doc_no = explode("/",$objdata['doc_no']);

    $doc_number = $str_doc_no[1];

    if($doc_number == '')
        $doc_number = '-';
    
    $data[] = array(
        'mainId'=>$objdata['main_id'],
        'edocId'=>$objdata['edoc_id'],
        'docNo'=>$objdata['doc_no'],
        'docNumber'=>$doc_number,
        'docDateDefault'=>$objdata['doc_date'],
        'docDate'=>$edocDateNew,
        'docTypeId'=>$objdata['edoc_type_id'],
        'docType'=>$objdata['edoc_type'],
        'docTypeName'=>$objdata['edoc_type_name'],
        'Headline'=>$objdata['headline'],
        'Receiver'=>$objdata['receiver'],
        'Comment'=>$objdata['comment'],

        'rapidId'=>$objdata['rapid_id'],
        'Rapid'=>$objdata['rapid_name'],
        'secretsId'=>$objdata['secrets_id'],
        'Secrets'=>$objdata['secrets_name'],

        'departName'=>$objdata['depart_name'],
        'departId_edoc'=>$objdata['depart_id'],
        'userMaker'=>$objdata['userFullname'],
        'senderDepart'=>$objdata['sender_depart'],
        'destroyYear'=>$objdata['destroy_year'],
        'dateWrite'=>$objdata['edoc_datewrite'],
        'edocStatus'=>$objdata['status'],
        'mainType'=>$objdata['main_type'],
        'mainStatus'=>$objdata['main_status']
    );
}

$data[] = array('Activity'=>$Activity);

header("Access-Control-Allow-Origin: *");
header("content-type:text/javascript;charset=utf-8");
header("Content-Type: application/json; charset=utf-8", true, 200);

print json_encode(array("data"=>$data));
